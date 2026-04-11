<?php
class FinanceModel extends Model {
    protected string $table = 'transactions';

    // ---- Helpers estáticos reutilizables ----
    public static function methodColors(): array {
        return [
            'cash'        => '#1D9E75',
            'credit_card' => '#378ADD',
            'debit_card'  => '#7F77DD',
            'transfer'    => '#EF9F27',
            'insurance'   => '#D85A30',
            'other'       => '#888780',
        ];
    }

    public static function methodLabel(string $method): string {
        return match($method) {
            'cash'        => 'Cash',
            'credit_card' => 'Credit Card',
            'debit_card'  => 'Debit Card',
            'transfer'    => 'Transfer',
            'insurance'   => 'Insurance',
            default       => 'Other',
        };
    }

    // ---- KPIs ----
    public function getKpis(): array {
        $currency = getSettingValue('currency', 'S/');

        $monthRevenue = $this->queryOne(
            "SELECT COALESCE(SUM(amount),0) as total
             FROM transactions
             WHERE status='paid'
               AND MONTH(paid_at)=MONTH(NOW())
               AND YEAR(paid_at)=YEAR(NOW())"
        );
        $prevRevenue = $this->queryOne(
            "SELECT COALESCE(SUM(amount),0) as total
             FROM transactions
             WHERE status='paid'
               AND MONTH(paid_at)=MONTH(DATE_SUB(NOW(),INTERVAL 1 MONTH))
               AND YEAR(paid_at)=YEAR(DATE_SUB(NOW(),INTERVAL 1 MONTH))"
        );
        $pending = $this->queryOne(
            "SELECT COALESCE(SUM(amount),0) as total, COUNT(*) as count
             FROM transactions WHERE status='pending'"
        );
        $avgTicket = $this->queryOne(
            "SELECT COALESCE(AVG(amount),0) as total
             FROM transactions
             WHERE status='paid'
               AND MONTH(paid_at)=MONTH(NOW())
               AND YEAR(paid_at)=YEAR(NOW())"
        );
        $totalTx = $this->queryOne(
            "SELECT COUNT(*) as total
             FROM transactions
             WHERE MONTH(created_at)=MONTH(NOW())
               AND YEAR(created_at)=YEAR(NOW())"
        );

        $curr  = (float)($monthRevenue['total'] ?? 0);
        $prev  = (float)($prevRevenue['total']  ?? 0);
        $delta = $prev > 0 ? round((($curr - $prev) / $prev) * 100, 1) : 0;

        return [
            'month_revenue'  => $curr,
            'revenue_delta'  => $delta,
            'pending_amount' => (float)($pending['total']   ?? 0),
            'pending_count'  => (int)($pending['count']     ?? 0),
            'avg_ticket'     => (float)($avgTicket['total'] ?? 0),
            'total_tx'       => (int)($totalTx['total']     ?? 0),
            'currency'       => $currency,
        ];
    }

    // ---- Charts ----
    public function getDailyRevenue(int $days = 14): array {
        return $this->query(
            "SELECT DATE(paid_at) as day,
                    DATE_FORMAT(paid_at,'%d/%m') as label,
                    COALESCE(SUM(amount),0) as total
             FROM transactions
             WHERE status='paid'
               AND paid_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY DATE(paid_at)
             ORDER BY DATE(paid_at)",
            [$days]
        );
    }

    public function getPaymentMethodBreakdown(): array {
        return $this->query(
            "SELECT payment_method,
                    COALESCE(SUM(amount),0) as total,
                    COUNT(*) as count
             FROM transactions
             WHERE status='paid'
               AND MONTH(paid_at)=MONTH(NOW())
               AND YEAR(paid_at)=YEAR(NOW())
             GROUP BY payment_method
             ORDER BY total DESC"
        );
    }

    public function getMonthlyEvolution(int $months = 8): array {
        return $this->query(
            "SELECT DATE_FORMAT(paid_at,'%b') as label,
                    MONTH(paid_at) as month_num,
                    YEAR(paid_at)  as year_num,
                    COALESCE(SUM(amount),0) as total
             FROM transactions
             WHERE status='paid'
               AND paid_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
             GROUP BY YEAR(paid_at), MONTH(paid_at)
             ORDER BY YEAR(paid_at), MONTH(paid_at)",
            [$months]
        );
    }

    // ---- Transactions list ----
    public function getAllTransactions(string $search = '', string $status = '', string $method = ''): array {
        $sql = "SELECT t.*,
                    CONCAT(p.first_name,' ',p.last_name) as patient_name,
                    pr.name as procedure_name,
                    a.date as appointment_date
                FROM transactions t
                JOIN patients p     ON t.patient_id     = p.id
                JOIN appointments a ON t.appointment_id = a.id
                JOIN procedures pr  ON a.procedure_id   = pr.id
                WHERE 1=1";
        $params = [];

        if ($search) {
            $sql .= " AND (p.first_name LIKE ? OR p.last_name LIKE ?)";
            $like = "%{$search}%";
            $params = array_merge($params, [$like, $like]);
        }
        if ($status) { $sql .= " AND t.status=?";           $params[] = $status; }
        if ($method) { $sql .= " AND t.payment_method=?";   $params[] = $method; }

        $sql .= " ORDER BY t.created_at DESC LIMIT 100";
        return $this->query($sql, $params);
    }

    // ---- Pending appointments (no payment yet) ----
    public function getPendingAppointments(): array {
        return $this->query(
            "SELECT a.id as appointment_id,
                    p.id as patient_id,
                    CONCAT(p.first_name,' ',p.last_name) as patient_name,
                    pr.name as procedure_name,
                    pr.price,
                    a.date,
                    u.name as dentist_name
             FROM appointments a
             JOIN patients p    ON a.patient_id   = p.id
             JOIN procedures pr ON a.procedure_id = pr.id
             JOIN dentists d    ON a.dentist_id   = d.id
             JOIN users u       ON d.user_id      = u.id
             WHERE a.status IN ('confirmed','completed')
               AND a.id NOT IN (
                 SELECT appointment_id FROM transactions WHERE appointment_id IS NOT NULL
               )
             ORDER BY a.date DESC
             LIMIT 50"
        );
    }

    // ---- Write operations ----
    public function registerPayment(array $data): int {
        return $this->insert($data);
    }

    public function updateStatus(int $id, string $status): bool {
        $data = ['status' => $status];
        if ($status === 'paid') {
            $data['paid_at'] = date('Y-m-d H:i:s');
        }
        return $this->update($id, $data);
    }
}
