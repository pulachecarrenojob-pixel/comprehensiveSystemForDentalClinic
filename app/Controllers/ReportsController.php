<?php
class ReportsController extends Controller {

    private ReportsModel $model;

    public function __construct() {
        $this->model = new ReportsModel();
    }

    public function index(): void {
        Auth::check();

        $from = $this->sanitize($this->input('from', date('Y-m-01')));
        $to   = $this->sanitize($this->input('to',   date('Y-m-t')));

        if ($from > $to) { [$from, $to] = [$to, $from]; }

        $kpis        = $this->model->getSummaryKpis($from, $to);
        $byDentist   = $this->model->getAppointmentsByDentist($from, $to);
        $revByDentist= $this->model->getRevenueByDentist($from, $to);
        $evolution   = $this->model->getMonthlyEvolution($from, $to);
        $procedures  = $this->model->getTopProcedures($from, $to);
        $performance = $this->model->getDentistPerformance($from, $to);
        $currency    = getSettingValue('currency', 'S/');

        $chartData = [
            'dentistLabels' => json_encode(array_column($byDentist,    'dentist_name')),
            'dentistAppts'  => json_encode(array_map('intval',   array_column($byDentist,    'total'))),
            'dentistColors' => json_encode(array_column($byDentist,    'color')),
            'revLabels'     => json_encode(array_column($revByDentist, 'dentist_name')),
            'revData'       => json_encode(array_map('floatval', array_column($revByDentist, 'total'))),
            'revColors'     => json_encode(array_column($revByDentist, 'color')),
            'evoLabels'     => json_encode(array_column($evolution,    'label')),
            'evoAppts'      => json_encode(array_map('intval',   array_column($evolution,    'appointments'))),
            'evoRevenue'    => json_encode(array_map('floatval', array_column($evolution,    'revenue'))),
            'procLabels'    => json_encode(array_column($procedures,   'name')),
            'procData'      => json_encode(array_map('intval',   array_column($procedures,   'total'))),
            'procColors'    => json_encode(array_column($procedures,   'color')),
        ];

        $this->view('reports/index', [
            'title'       => 'Reports',
            'kpis'        => $kpis,
            'byDentist'   => $byDentist,
            'revByDentist'=> $revByDentist,
            'evolution'   => $evolution,
            'procedures'  => $procedures,
            'performance' => $performance,
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

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="report_' . $from . '_' . $to . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');

        // BOM for Excel UTF-8
        fputs($out, "\xEF\xBB\xBF");

        fputcsv($out, ['DentalCare — Report', 'From: ' . $from, 'To: ' . $to]);
        fputcsv($out, []);
        fputcsv($out, ['SUMMARY']);
        fputcsv($out, ['Metric', 'Value']);
        fputcsv($out, ['Total Appointments', $kpis['total_appts']]);
        fputcsv($out, ['Completed',          $kpis['attended']]);
        fputcsv($out, ['Attendance Rate',    $kpis['attend_rate'] . '%']);
        fputcsv($out, ['Total Revenue',      $currency . ' ' . number_format($kpis['revenue'], 2)]);
        fputcsv($out, []);

        fputcsv($out, ['DENTIST PERFORMANCE']);
        fputcsv($out, ['Dentist', 'Specialty', 'Total', 'Completed', 'Cancelled', 'Rate %', 'Revenue']);
        foreach ($performance as $p) {
            fputcsv($out, [
                $p['dentist_name'],
                $p['specialty'],
                $p['total'],
                $p['completed'],
                $p['cancelled'],
                $p['rate'] . '%',
                $currency . ' ' . number_format((float)$p['revenue'], 2),
            ]);
        }
        fputcsv($out, []);

        fputcsv($out, ['TOP PROCEDURES']);
        fputcsv($out, ['Procedure', 'Category', 'Count', 'Revenue']);
        foreach ($procedures as $p) {
            fputcsv($out, [
                $p['name'],
                $p['category'],
                $p['total'],
                $currency . ' ' . number_format((float)$p['revenue'], 2),
            ]);
        }

        fclose($out);
        exit;
    }
}
