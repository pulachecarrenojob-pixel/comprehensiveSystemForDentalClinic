<?php
class DashboardModel extends Model {
    protected string $table = 'appointments';

    public function getKpis(): array {
        $today = date('Y-m-d');

        // Today's appointments
        $todayAppts = $this->queryOne(
            "SELECT COUNT(*) as total FROM appointments WHERE date = ? AND status != 'cancelled'",
            [$today]
        );

        // Total active patients
        $totalPatients = $this->queryOne(
            "SELECT COUNT(*) as total FROM patients WHERE is_active = 1"
        );

        // Monthly revenue
        $monthlyRevenue = $this->queryOne(
            "SELECT COALESCE(SUM(amount),0) as total FROM transactions
             WHERE status = 'paid' AND MONTH(paid_at) = MONTH(NOW()) AND YEAR(paid_at) = YEAR(NOW())"
        );

        // Confirmed today
        $confirmed = $this->queryOne(
            "SELECT COUNT(*) as total FROM appointments WHERE date = ? AND status = 'confirmed'",
            [$today]
        );

        // Previous month revenue for delta
        $prevRevenue = $this->queryOne(
            "SELECT COALESCE(SUM(amount),0) as total FROM transactions
             WHERE status = 'paid'
             AND MONTH(paid_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
             AND YEAR(paid_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))"
        );

        $curr = (float)($monthlyRevenue['total'] ?? 0);
        $prev = (float)($prevRevenue['total'] ?? 0);
        $delta = $prev > 0 ? round((($curr - $prev) / $prev) * 100, 1) : 0;

        return [
            'today_appointments' => (int)($todayAppts['total'] ?? 0),
            'total_patients'     => (int)($totalPatients['total'] ?? 0),
            'monthly_revenue'    => $curr,
            'confirmed_today'    => (int)($confirmed['total'] ?? 0),
            'revenue_delta'      => $delta,
        ];
    }

    public function getPatientsPerDay(): array {
        $rows = $this->query(
            "SELECT DAYNAME(date) as day_name, DAYOFWEEK(date) as day_num,
                    COUNT(*) as total
             FROM appointments
             WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
               AND status != 'cancelled'
             GROUP BY DAYOFWEEK(date), DAYNAME(date)
             ORDER BY DAYOFWEEK(date)"
        );
        $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        $result = [];
        $map = array_column($rows, 'total', 'day_name');
        foreach ($days as $d) {
            $result[] = ['day' => substr($d, 0, 3), 'total' => (int)($map[$d] ?? 0)];
        }
        return $result;
    }

    public function getRevenueEvolution(): array {
        return $this->query(
            "SELECT DATE_FORMAT(paid_at, '%b') as month,
                    MONTH(paid_at) as month_num,
                    COALESCE(SUM(amount), 0) as total
             FROM transactions
             WHERE status = 'paid'
               AND paid_at >= DATE_SUB(NOW(), INTERVAL 8 MONTH)
             GROUP BY MONTH(paid_at), DATE_FORMAT(paid_at, '%b')
             ORDER BY MONTH(paid_at)"
        );
    }

    public function getProcedureBreakdown(): array {
        return $this->query(
            "SELECT p.name, p.color, COUNT(cr.id) as total
             FROM clinical_records cr
             JOIN procedures p ON cr.procedure_id = p.id
             GROUP BY p.id, p.name, p.color
             ORDER BY total DESC
             LIMIT 6"
        );
    }

    public function getTodayAppointments(): array {
        return $this->query(
            "SELECT a.*,
                    CONCAT(pa.first_name,' ',pa.last_name) as patient_name,
                    CONCAT(u.name) as dentist_name,
                    pr.name as procedure_name,
                    pr.color as procedure_color
             FROM appointments a
             JOIN patients pa ON a.patient_id = pa.id
             JOIN dentists d  ON a.dentist_id = d.id
             JOIN users u     ON d.user_id = u.id
             JOIN procedures pr ON a.procedure_id = pr.id
             WHERE a.date = CURDATE()
             ORDER BY a.start_time"
        );
    }

    public function getRecentActivity(): array {
        return $this->query(
            "SELECT 'appointment' as type,
                    CONCAT(pa.first_name,' ',pa.last_name) as name,
                    pr.name as detail,
                    a.created_at as time
             FROM appointments a
             JOIN patients pa ON a.patient_id = pa.id
             JOIN procedures pr ON a.procedure_id = pr.id
             ORDER BY a.created_at DESC LIMIT 8"
        );
    }
}
