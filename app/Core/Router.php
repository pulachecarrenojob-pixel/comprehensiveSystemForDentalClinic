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
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Strip base path from URI
        $basePath = parse_url(BASE_URL, PHP_URL_PATH);
        if ($basePath && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
        $uri = '/' . ltrim($uri, '/');
        if ($uri !== '/' ) $uri = rtrim($uri, '/');

        $handler = self::$routes[$method][$uri] ?? null;

        if (!$handler) {
            http_response_code(404);
            if (file_exists(BASE_PATH . '/views/errors/404.php')) {
                require BASE_PATH . '/views/errors/404.php';
            } else {
                echo '<h1>404 - Page not found</h1>';
            }
            return;
        }

        [$controllerName, $methodName] = explode('@', $handler);
        $controllerFile = BASE_PATH . '/app/Controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            die("Controller not found: {$controllerName}");
        }

        require_once $controllerFile;
        $controller = new $controllerName();

        if (!method_exists($controller, $methodName)) {
            die("Method not found: {$controllerName}@{$methodName}");
        }

        $controller->$methodName();
    }
}
