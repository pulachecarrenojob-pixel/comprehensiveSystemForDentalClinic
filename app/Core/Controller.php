<?php
class Controller {
    protected function view(string $viewPath, array $data = []): void {
        extract($data);
        $viewFile = BASE_PATH . '/views/' . $viewPath . '.php';
        if (!file_exists($viewFile)) {
            die('View not found: ' . $viewPath);
        }
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $layout     = $data['layout'] ?? 'layouts/main';
        $layoutFile = BASE_PATH . '/views/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function json(mixed $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $path): void {
        $path = ltrim($path, '/');
        header('Location: ' . BASE_URL . '/index.php?url=' . $path);
        exit;
    }

    protected function back(): void {
        $ref = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/index.php?url=dashboard');
        header('Location: ' . $ref);
        exit;
    }

    protected function flash(string $type, string $message): void {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function input(string $key, mixed $default = ''): mixed {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function sanitize(string $value): string {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
