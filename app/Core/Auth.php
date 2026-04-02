<?php
class Auth {
    public static function check(): void {
        if (!isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public static function guest(): void {
        if (isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }

    public static function user(): array|null {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): int|null {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function role(): string|null {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function isAdmin(): bool {
        return self::role() === 'admin';
    }

    public static function isDentist(): bool {
        return self::role() === 'dentist';
    }

    public static function login(array $user): void {
        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];
        session_regenerate_id(true);
    }

    public static function logout(): void {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public static function csrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
                http_response_code(403);
                die('CSRF token mismatch.');
            }
        }
    }
}
