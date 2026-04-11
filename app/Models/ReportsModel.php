<?php
class ReportsModel extends Model {
    protected string $table = 'appointments';

    public function getSummaryKpis(string $from, string $to): array {
        $appts = $this->queryOne(
            "SELECT COUNT(*) as total FROM appointments
             WHERE date BETWEEN ? AND ? AND status != 'cancelled'",
            [$from, $to]
        );
        $attended = $this->queryOne(
            "SELECT COUNT(*) as total FROM appointments
             WHERE date BETWEEN ? AND ? AND status = 'completed'",
            [$from, $to]
        );
        $revenue = $this->queryOne(
            "SELECT COALESCE(SUM(t.amount),0) as total
             FROM transactions t
             JOIN appointments a ON t.appointment_id = a.id
             WHERE a.date BETWEEN ? AND ? AND t.status = 'paid'",
            [$from, $to]
        );
        $rate = (int)($appts['total'] ?? 0) > 0
            ? round(((int)($attended['total'] ?? 0) / (int)($appts['total'] ?? 1)) * 100)
            : 0;

        return [
            'total_appts'  => (int)($appts['total']    ?? 0),
            'attended'     => (int)($attended['total'] ?? 0),
            'revenue'      => (float)($revenue['total'] ?? 0),
            'attend_rate'  => $rate,
        ];
    }

    public function getAppointmentsByDentist(string $from, string $to): array {
        return $this->query(
            "SELECT u.name as dentist_name, d.color,
                    COUNT(*) as total,
                    SUM(CASE WHEN a.status='completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN a.status='cancelled' THEN 1 ELSE 0 END) as cancelled
             FROM appointments a
             JOIN dentists d ON a.dentist_id = d.id
             JOIN users u    ON d.user_id    = u.id
             WHERE a.date BETWEEN ? AND ?
             GROUP BY d.id, u.name, d.color
             ORDER BY total DESC",
            [$from, $to]
        );
    }

    public function getRevenueByDentist(string $from, string $to): array {
        return $this->query(
            "SELECT u.name as dentist_name, d.color,
                    COALESCE(SUM(t.amount),0) as total
             FROM transactions t
             JOIN appointments a ON t.appointment_id = a.id
             JOIN dentists d     ON a.dentist_id     = d.id
             JOIN users u        ON d.user_id        = u.id
             WHERE a.date BETWEEN ? AND ? AND t.status='paid'
             GROUP BY d.id, u.name, d.color
             ORDER BY total DESC",
            [$from, $to]
        );
    }

    public function getMonthlyEvolution(string $from, string $to): array {
        return $this->query(
            "SELECT DATE_FORMAT(a.date,'%b %Y') as label,
                    MONTH(a.date) as month_num,
                    YEAR(a.date)  as year_num,
                    COUNT(*) as appointments,
                    COALESCE(SUM(t.amount),0) as revenue
             FROM appointments a
             LEFT JOIN transactions t ON t.appointment_id = a.id AND t.status='paid'
             WHERE a.date BETWEEN ? AND ? AND a.status != 'cancelled'
             GROUP BY YEAR(a.date), MONTH(a.date)
             ORDER BY YEAR(a.date), MONTH(a.date)",
            [$from, $to]
        );
    }

    public function getTopProcedures(string $from, string $to, int $limit = 8): array {
        return $this->query(
            "SELECT pr.name, pr.color, pr.category,
                    COUNT(*) as total,
                    COALESCE(SUM(t.amount),0) as revenue
             FROM appointments a
             JOIN procedures pr ON a.procedure_id = pr.id
             LEFT JOIN transactions t ON t.appointment_id = a.id AND t.status='paid'
             WHERE a.date BETWEEN ? AND ? AND a.status != 'cancelled'
             GROUP BY pr.id, pr.name, pr.color, pr.category
             ORDER BY total DESC
             LIMIT ?",
            [$from, $to, $limit]
        );
    }

    public function getDentistPerformance(string $from, string $to): array {
        return $this->query(
            "SELECT u.name as dentist_name, d.specialty, d.color,
                    COUNT(*) as total,
                    SUM(CASE WHEN a.status='completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN a.status='cancelled' THEN 1 ELSE 0 END) as cancelled,
                    COALESCE(SUM(t.amount),0) as revenue,
                    ROUND(
                      SUM(CASE WHEN a.status='completed' THEN 1 ELSE 0 END)
                      / NULLIF(COUNT(*),0) * 100
                    ) as rate
             FROM appointments a
             JOIN dentists d ON a.dentist_id = d.id
             JOIN users u    ON d.user_id    = u.id
             LEFT JOIN transactions t ON t.appointment_id = a.id AND t.status='paid'
             WHERE a.date BETWEEN ? AND ?
             GROUP BY d.id, u.name, d.specialty, d.color
             ORDER BY revenue DESC",
            [$from, $to]
        );
    }

    public function getAllDentists(): array {
        return $this->query(
            "SELECT d.id, u.name FROM dentists d
             JOIN users u ON d.user_id=u.id
             WHERE d.is_active=1 ORDER BY u.name"
        );
    }
}
