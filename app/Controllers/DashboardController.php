<?php
class DashboardController extends Controller {

    public function index(): void {
        Auth::check();
        $this->view('dashboard/index', [
            'title' => 'Dashboard',
        ]);
    }
}
