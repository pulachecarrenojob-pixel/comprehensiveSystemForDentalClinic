<?php
class FinanceController extends Controller {

    private FinanceModel $model;

    public function __construct() {
        $this->model = new FinanceModel();
    }

    public function index(): void {
        Auth::check();

        $search   = $this->sanitize($this->input('search', ''));
        $status   = $this->sanitize($this->input('status', ''));
        $method   = $this->sanitize($this->input('method', ''));

        // Data
        $kpis         = $this->model->getKpis();
        $daily        = $this->model->getDailyRevenue(14);
        $methodBreak  = $this->model->getPaymentMethodBreakdown();
        $transactions = $this->model->getAllTransactions($search, $status, $method);
        $pending      = $this->model->getPendingAppointments();
        $colors       = FinanceModel::methodColors();

        // Build method breakdown with labels and colors already resolved
        // so the view never needs to call any model method
        $methodBreakFormatted = array_map(function($m) use ($colors) {
            return [
                'method'  => $m['payment_method'],
                'label'   => FinanceModel::methodLabel($m['payment_method']),
                'total'   => (float)$m['total'],
                'count'   => (int)$m['count'],
                'color'   => $colors[$m['payment_method']] ?? '#888780',
            ];
        }, $methodBreak);

        // Build transactions with labels and colors already resolved
        $transactionsFormatted = array_map(function($t) use ($colors) {
            return array_merge($t, [
                'method_label' => FinanceModel::methodLabel($t['payment_method']),
                'method_color' => $colors[$t['payment_method']] ?? '#888780',
            ]);
        }, $transactions);

        // Chart data — prepared in controller, passed as JSON strings
        $chartData = [
            'dailyLabels'  => json_encode(array_column($daily, 'label')),
            'dailyData'    => json_encode(array_map('floatval', array_column($daily, 'total'))),
            'methodLabels' => json_encode(array_column($methodBreakFormatted, 'label')),
            'methodData'   => json_encode(array_column($methodBreakFormatted, 'total')),
            'methodColors' => json_encode(array_column($methodBreakFormatted, 'color')),
        ];

        $this->view('finance/index', [
            'title'                => 'Finance',
            'kpis'                 => $kpis,
            'methodBreakFormatted' => $methodBreakFormatted,
            'transactions'         => $transactionsFormatted,
            'pending'              => $pending,
            'search'               => $search,
            'statusFilter'         => $status,
            'methodFilter'         => $method,
            'chartData'            => $chartData,
            'extraJs'              => ['finance.js'],
            'extraCss'             => ['finance.css'],
        ]);
    }

    public function store(): void {
        Auth::check();
        Auth::verifyCsrf();

        $appointmentId = (int)$this->input('appointment_id');
        $patientId     = (int)$this->input('patient_id');
        $amount        = (float)str_replace(',', '.', $this->input('amount', '0'));
        $method        = $this->sanitize($this->input('payment_method', 'cash'));
        $notes         = $this->sanitize($this->input('notes', ''));

        if (!$appointmentId || !$patientId || $amount <= 0) {
            flash('error', 'Appointment, patient and amount are required.');
            $this->redirect('/finance');
        }

        $this->model->registerPayment([
            'appointment_id' => $appointmentId,
            'patient_id'     => $patientId,
            'amount'         => $amount,
            'payment_method' => $method,
            'status'         => 'paid',
            'paid_at'        => date('Y-m-d H:i:s'),
            'notes'          => $notes,
            'created_by'     => Auth::id(),
        ]);

        flash('success', 'Payment registered successfully.');
        $this->redirect('/finance');
    }

    public function update(): void {
        Auth::check();
        Auth::verifyCsrf();

        $id     = (int)$this->input('id');
        $status = $this->sanitize($this->input('status'));

        $this->model->updateStatus($id, $status);
        flash('success', 'Transaction updated.');
        $this->redirect('/finance');
    }
}
