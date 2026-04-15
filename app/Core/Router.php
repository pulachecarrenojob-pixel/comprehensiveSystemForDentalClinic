<?php
class Router {
    private static array $routes = [];

    public static function get(string $path, string $handler): void {
        self::$routes['GET'][$path] = $handler;
    }

    public static function post(string $path, string $handler): void {
        self::$routes['POST'][$path] = $handler;
    }

    public static function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];

        // Read from ?url= parameter — always
        $uri = trim($_GET['url'] ?? '', '/');

        // Default to dashboard
        if ($uri === '') {
            $uri = 'dashboard';
        }

        // Normalize: add leading slash to match routes
        $uri = '/' . $uri;

        $handler = self::$routes[$method][$uri] ?? null;

        if (!$handler) {
            http_response_code(404);
            if (file_exists(BASE_PATH . '/views/errors/404.php')) {
                require BASE_PATH . '/views/errors/404.php';
            } else {
                echo '<h1>404 — Page not found</h1>';
                echo '<p>Route not found: <code>' . htmlspecialchars($uri) . '</code></p>';
            }
            return;
        }

        [$controllerName, $methodName] = explode('@', $handler);
        $controllerFile = BASE_PATH . '/app/Controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            die('Controller not found: ' . htmlspecialchars($controllerName));
        }

        require_once $controllerFile;
        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            die('Method not found: ' . htmlspecialchars($controllerName . '@' . $methodName));
        }

        $controller->$methodName();
    }
}
