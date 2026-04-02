<?php
define('APP_NAME',    'DentalCare');
define('APP_VERSION', '1.0.0');
define('BASE_URL',    'http://localhost/dentalcare/public');
define('BASE_PATH',   dirname(__DIR__));
define('DEBUG_MODE',  true);

date_default_timezone_set('America/Lima');

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}
