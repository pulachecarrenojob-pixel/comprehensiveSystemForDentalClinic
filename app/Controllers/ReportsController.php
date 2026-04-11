<?php
class ReportsController extends Controller {

    private ReportsModel $model;

    public function __construct() {
        $this->model = new ReportsModel();
    }

    public function index(): void {
        Auth::check();

        // Date range — default current month
        $from = $this->sanitize($this->input('from', date('Y-m-01')));
        $to   = $this->sanitize($this->input('to',   date('Y-m-t')));

        // Clamp dates
        if ($from > $to) { $tmp = $from; $from = $to; $to = $tmp; }

        $kpis          = $this->model->getSummaryKpis($from, $to);
        $byDentist     = $this->model->getAppointmentsByDentist($from, $to);
        $revByDentist  = $this->model->getRevenueByDentist($from, $to);
        $evolution     = $this->model->getMonthlyEvolution($from, $to);
        $procedures    = $this->model->getTopProcedures($from, $to);
        $performance   = $this->model->getDentistPerformance($from, $to);
        $dentists      = $this->model->getAllDentists();
        $currency      = getSettingValue('currency', 'S/');

        // Chart data
        $chartData = [
            // Bar: appointments by dentist
            'dentistLabels'  => json_encode(array_column($byDentist, 'dentist_name')),
            'dentistAppts'   => json_encode(array_map('intval', array_column($byDentist, 'total'))),
            'dentistColors'  => json_encode(array_column($byDentist, 'color')),
            // Bar: revenue by dentist
            'revLabels'      => json_encode(array_column($revByDentist, 'dentist_name')),
            'revData'        => json_encode(array_map('floatval', array_column($revByDentist, 'total'))),
            'revColors'      => json_encode(array_column($revByDentist, 'color')),
            // Line: monthly evolution
            'evoLabels'      => json_encode(array_column($evolution, 'label')),
            'evoAppts'       => json_encode(array_map('intval',   array_column($evolution, 'appointments'))),
            'evoRevenue'     => json_encode(array_map('floatval', array_column($evolution, 'revenue'))),
            // Donut: top procedures
            'procLabels'     => json_encode(array_column($procedures, 'name')),
            'procData'       => json_encode(array_map('intval', array_column($procedures, 'total'))),
            'procColors'     => json_encode(array_column($procedures, 'color')),
        ];

        $this->view('reports/index', [
            'title'       => 'Reports',
            'kpis'        => $kpis,
            'byDentist'   => $byDentist,
            'revByDentist'=> $revByDentist,
            'evolution'   => $evolution,
            'procedures'  => $procedures,
            'performance' => $performance,
            'dentists'    => $dentists,
            'from'        => $from,
            'to'          => $to,
            'currency'    => $currency,
            'chartData'   => $chartData,
            'extraJs'     => ['reports.js'],
            'extraCss'    => ['reports.css'],
        ]);
    }

    public function export(): void {
        Auth::check();
        $from = $this->sanitize($this->input('from', date('Y-m-01')));
        $to   = $this->sanitize($this->input('to',   date('Y-m-t')));

        $performance = $this->model->getDentistPerformance($from, $to);
        $procedures  = $this->model->getTopProcedures($from, $to);
        $kpis        = $this->model->getSummaryKpis($from, $to);
        $currency    = getSettingValue('currency', 'S/');

        // Simple CSV export
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="report_'.$from.'_'.$to.'.csv"');

        $out = fopen('php://output', 'w');

        // Summary
        fputcsv($out, ['DentalCare Report', $from, 'to', $to]);
        fputcsv($out, []);
        fputcsv($out, ['SUMMARY']);
        fputcsv($out, ['Total Appointments', $kpis['total_appts']]);
        fputcsv($out, ['Completed',          $kpis['attended']]);
        fputcsv($out, ['Revenue',            $currency.' '.number_format($kpis['revenue'],2)]);
        fputcsv($out, ['Attendance Rate',    $kpis['attend_rate'].'%']);
        fputcsv($out, []);

        // Performance by dentist
        fputcsv($out, ['DENTIST PERFORMANCE']);
        fputcsv($out, ['Dentist','Specialty','Total','Completed','Cancelled','Rate%','Revenue']);
        foreach($performance as $p) {
            fputcsv($out, [
                $p['dentist_name'], $p['specialty'],
                $p['total'], $p['completed'], $p['cancelled'],
                $p['rate'].'%',
                $currency.' '.number_format((float)$p['revenue'],2),
            ]);
        }
        fputcsv($out, []);

        // Top procedures
        fputcsv($out, ['TOP PROCEDURES']);
        fputcsv($out, ['Procedure','Category','Count','Revenue']);
        foreach($procedures as $p) {
            fputcsv($out, [
                $p['name'], $p['category'], $p['total'],
                $currency.' '.number_format((float)$p['revenue'],2),
            ]);
        }

        fclose($out);
        exit;
    }
}
