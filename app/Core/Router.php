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

        // Always read from ?url= parameter
        $uri = '/' . trim($_GET['url'] ?? '', '/');

        // Normalize: remove trailing slash unless root
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        // Default route
        if ($uri === '/' || $uri === '') {
            $uri = '/dashboard';
        }

        $handler = self::$routes[$method][$uri] ?? null;

        if (!$handler) {
            // Try without leading slash as fallback
            $handler = self::$routes[$method][ltrim($uri, '/')] ?? null;
        }

        if (!$handler) {
            http_response_code(404);
            if (file_exists(BASE_PATH . '/views/errors/404.php')) {
                require BASE_PATH . '/views/errors/404.php';
            } else {
                echo '<h1>404 — Page not found</h1><p>Route not found: ' . htmlspecialchars($uri) . '</p>';
            }
            return;
        }

        [$controllerName, $methodName] = explode('@', $handler);
        $controllerFile = BASE_PATH . '/app/Controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            die('Controller not found: ' . $controllerName);
        }

        require_once $controllerFile;
        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            die('Method not found: ' . $controllerName . '@' . $methodName);
        }

        $controller->$methodName();
    }
}
