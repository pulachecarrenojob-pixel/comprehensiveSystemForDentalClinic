<?php
// Load configuration
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';

// Load Router first (needed before routes.php)
require_once BASE_PATH . '/app/Core/Router.php';

// Boot application
require_once BASE_PATH . '/app/Core/App.php';
new App();
