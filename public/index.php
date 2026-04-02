<?php
require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once BASE_PATH . '/app/Core/Router.php';
require_once BASE_PATH . '/app/Core/App.php';
new App();
