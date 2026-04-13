<?php
// ============================================================
//  DentalCare — app/Helpers/functions.php
// ============================================================

function clean(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generates a URL compatible with the router.
 * The router reads $_GET['url'], so all links must use ?url=path.
 * Example: url('patients') => http://localhost/dentalcare/public/index.php?url=patients
 */
function url(string $path = ''): string {
    $path = ltrim($path, '/');
    if ($path === '' || $path === 'dashboard') {
        return BASE_URL . '/index.php?url=dashboard';
    }
    return BASE_URL . '/index.php?url=' . $path;
}

/**
 * url() with extra GET params
 * Example: urlQ('reports', ['from'=>'2026-01-01'])
 */
function urlQ(string $path, array $params = []): string {
    $base = url($path);
    if (!empty($params)) {
        $base .= '&' . http_build_query($params);
    }
    return $base;
}

function asset(string $path): string {
    return BASE_URL . '/assets/' . ltrim($path, '/');
}

function redirect(string $path): void {
    header('Location: ' . url($path));
    exit;
}

function formatMoney(float $amount, string $currency = null): string {
    $symbol = $currency ?? getSettingValue('currency', 'S/');
    return $symbol . ' ' . number_format($amount, 2);
}

function formatDate(string $date, string $format = 'd/m/Y'): string {
    if (empty($date)) return '—';
    return date($format, strtotime($date));
}

function formatDateTime(string $datetime, string $format = 'd/m/Y H:i'): string {
    if (empty($datetime)) return '—';
    return date($format, strtotime($datetime));
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
    $currentUrl = $_GET['url'] ?? 'dashboard';
    $path       = ltrim($path, '/');
    if ($path === 'dashboard' && ($currentUrl === '' || $currentUrl === 'dashboard')) {
        return 'active';
    }
    return str_starts_with($currentUrl, $path) ? 'active' : '';
}

function getSettingValue(string $key, string $default = ''): string {
    static $settings = null;
    if ($settings === null) {
        try {
            $db   = Database::getInstance();
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
    if (count($parts) > 1) {
        $init .= strtoupper(substr(end($parts), 0, 1));
    }
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
