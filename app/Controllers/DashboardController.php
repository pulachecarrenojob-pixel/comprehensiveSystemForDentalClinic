<?php
class DashboardController extends Controller {

    private DashboardModel $model;

    public function __construct() {
        $this->model = new DashboardModel();
    }

    public function index(): void {
        Auth::check();

        $kpis            = $this->model->getKpis();
        $patientsPerDay  = $this->model->getPatientsPerDay();
        $revenueEvol     = $this->model->getRevenueEvolution();
        $procedures      = $this->model->getProcedureBreakdown();
        $todayAppts      = $this->model->getTodayAppointments();

        $this->view('dashboard/index', [
            'title'          => 'Dashboard',
            'kpis'           => $kpis,
            'patientsPerDay' => $patientsPerDay,
            'revenueEvol'    => $revenueEvol,
            'procedures'     => $procedures,
            'todayAppts'     => $todayAppts,
            'extraJs'        => ['dashboard.js'],
        ]);
    }
}
