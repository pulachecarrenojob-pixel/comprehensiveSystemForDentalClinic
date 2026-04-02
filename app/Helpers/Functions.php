<?php
function clean(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void {
    header('Location: ' . BASE_URL . $path);
    exit;
}

function asset(string $path): string {
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

function url(string $path = ''): string {
    return BASE_URL . '/' . ltrim($path, '/');
}

function formatMoney(float $amount, string $currency = null): string {
    $symbol = $currency ?? getSettingValue('currency', '$');
    return $symbol . ' ' . number_format($amount, 2);
}

function formatDate(string $date, string $format = 'd/m/Y'): string {
    return date($format, strtotime($date));
}

function formatDateTime(string $datetime, string $format = 'd/m/Y H:i'): string {
    return date($format, strtotime($datetime));
}

function timeAgo(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff / 60) . ' min ago';
    if ($diff < 86400)  return floor($diff / 3600) . ' hours ago';
    return floor($diff / 86400) . ' days ago';
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): array|null {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function csrfField(): string {
    $token = Auth::csrfToken();
    return '<input type="hidden" name="_token" value="' . $token . '">';
}

function isActive(string $path): string {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $basePath = parse_url(BASE_URL, PHP_URL_PATH);
    $cleanUri = str_replace($basePath, '', $uri);
    return str_starts_with($cleanUri ?: '/', $path) ? 'active' : '';
}

function getSettingValue(string $key, string $default = ''): string {
    static $settings = null;
    if ($settings === null) {
        try {
            $db = Database::getInstance();
            $stmt = $db->query("SELECT key_name, value FROM settings");
            $settings = [];
            foreach ($stmt->fetchAll() as $row) {
                $settings[$row['key_name']] = $row['value'];
            }
        } catch (Exception $e) {
            return $default;
        }
    }
    return $settings[$key] ?? $default;
}

function initials(string $name): string {
    $parts = explode(' ', trim($name));
    $init  = strtoupper(substr($parts[0], 0, 1));
    if (count($parts) > 1) $init .= strtoupper(substr(end($parts), 0, 1));
    return $init;
}

function statusBadge(string $status): string {
    $map = [
        'scheduled'  => ['label' => 'Scheduled',  'class' => 'badge-info'],
        'confirmed'  => ['label' => 'Confirmed',   'class' => 'badge-success'],
        'completed'  => ['label' => 'Completed',   'class' => 'badge-primary'],
        'cancelled'  => ['label' => 'Cancelled',   'class' => 'badge-danger'],
        'no_show'    => ['label' => 'No Show',     'class' => 'badge-warning'],
        'paid'       => ['label' => 'Paid',        'class' => 'badge-success'],
        'pending'    => ['label' => 'Pending',     'class' => 'badge-warning'],
        'refunded'   => ['label' => 'Refunded',    'class' => 'badge-danger'],
    ];
    $s = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'badge-secondary'];
    return '<span class="badge ' . $s['class'] . '">' . $s['label'] . '</span>';
}
