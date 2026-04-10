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

        $kpis         = $this->model->getKpis();
        $daily        = $this->model->getDailyRevenue(14);
        $methodBreak  = $this->model->getPaymentMethodBreakdown();
        $transactions = $this->model->getAllTransactions($search, $status, $method);
        $pending      = $this->model->getPendingAppointments();
        $evolution    = $this->model->getMonthlyEvolution(8);
        $colors       = $this->model->getPaymentMethodColors();

        // Prepare chart data
        $dailyLabels = array_column($daily, 'label');
        $dailyData   = array_column($daily, 'total');
        $evoLabels   = array_column($evolution, 'label');
        $evoData     = array_column($evolution, 'total');
        $methodLabels= array_map(
            fn($m) => $this->model->formatMethod($m['payment_method']),
            $methodBreak
        );
        $methodData  = array_column($methodBreak, 'total');
        $methodColors= array_map(
            fn($m) => $colors[$m['payment_method']] ?? '#888',
            $methodBreak
        );

        $this->view('finance/index', [
            'title'        => 'Finance',
            'kpis'         => $kpis,
            'transactions' => $transactions,
            'pending'      => $pending,
            'methodBreak'  => $methodBreak,
            'search'       => $search,
            'statusFilter' => $status,
            'methodFilter' => $method,
            'dailyLabels'  => json_encode($dailyLabels),
            'dailyData'    => json_encode(array_map('floatval', $dailyData)),
            'evoLabels'    => json_encode($evoLabels),
            'evoData'      => json_encode(array_map('floatval', $evoData)),
            'methodLabels' => json_encode($methodLabels),
            'methodData'   => json_encode(array_map('floatval', $methodData)),
            'methodColors' => json_encode($methodColors),
            'extraJs'      => ['finance.js'],
            'extraCss'     => ['finance.css'],
        ]);
    }

    public function store(): void {
        Auth::check();
        Auth::verifyCsrf();

        $appointmentId = (int)$this->input('appointment_id');
        $patientId     = (int)$this->input('patient_id');
        $amount        = (float)$this->input('amount');
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
