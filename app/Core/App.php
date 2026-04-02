<?php
class App {
    public function __construct() {
        session_start();
        $this->loadCore();
        $this->loadHelpers();
        $this->loadModels();
        require BASE_PATH . '/config/routes.php';
        Router::dispatch();
    }

    private function loadCore(): void {
        $coreFiles = [
            'Database',
            'Model',
            'Auth',
            'Controller',
        ];
        foreach ($coreFiles as $file) {
            require_once BASE_PATH . '/app/Core/' . $file . '.php';
        }
    }

    private function loadHelpers(): void {
        $helperDir = BASE_PATH . '/app/Helpers/';
        if (is_dir($helperDir)) {
            foreach (glob($helperDir . '*.php') as $file) {
                require_once $file;
            }
        }
    }

    private function loadModels(): void {
        $modelDir = BASE_PATH . '/app/Models/';
        if (is_dir($modelDir)) {
            foreach (glob($modelDir . '*.php') as $file) {
                require_once $file;
            }
        }
    }
}
