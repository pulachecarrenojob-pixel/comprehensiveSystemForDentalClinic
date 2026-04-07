<?php
class ScheduleModel extends Model {
    protected string $table = 'appointments';

    public function getWeekAppointments(string $startDate, string $endDate): array {
        return $this->query(
            "SELECT a.*,
                    CONCAT(pa.first_name,' ',pa.last_name) as patient_name,
                    pa.phone as patient_phone,
                    u.name as dentist_name,
                    d.specialty as dentist_specialty,
                    d.color as dentist_color,
                    pr.name as procedure_name,
                    pr.color as procedure_color,
                    pr.duration as procedure_duration
             FROM appointments a
             JOIN patients pa   ON a.patient_id   = pa.id
             JOIN dentists d    ON a.dentist_id    = d.id
             JOIN users u       ON d.user_id       = u.id
             JOIN procedures pr ON a.procedure_id  = pr.id
             WHERE a.date BETWEEN ? AND ?
               AND a.status != 'cancelled'
             ORDER BY a.date, a.start_time",
            [$startDate, $endDate]
        );
    }

    public function getDayAppointments(string $date): array {
        return $this->query(
            "SELECT a.*,
                    CONCAT(pa.first_name,' ',pa.last_name) as patient_name,
                    u.name as dentist_name,
                    d.color as dentist_color,
                    pr.name as procedure_name,
                    pr.color as procedure_color
             FROM appointments a
             JOIN patients pa   ON a.patient_id   = pa.id
             JOIN dentists d    ON a.dentist_id    = d.id
             JOIN users u       ON d.user_id       = u.id
             JOIN procedures pr ON a.procedure_id  = pr.id
             WHERE a.date = ? AND a.status != 'cancelled'
             ORDER BY a.start_time",
            [$date]
        );
    }

    public function getById(int $id): array|false {
        return $this->queryOne(
            "SELECT a.*,
                    CONCAT(pa.first_name,' ',pa.last_name) as patient_name,
                    u.name as dentist_name,
                    pr.name as procedure_name,
                    pr.duration as procedure_duration
             FROM appointments a
             JOIN patients pa   ON a.patient_id   = pa.id
             JOIN dentists d    ON a.dentist_id    = d.id
             JOIN users u       ON d.user_id       = u.id
             JOIN procedures pr ON a.procedure_id  = pr.id
             WHERE a.id = ?",
            [$id]
        );
    }

    public function getAllDentists(): array {
        return $this->query(
            "SELECT d.*, u.name, d.color, d.specialty
             FROM dentists d
             JOIN users u ON d.user_id = u.id
             WHERE d.is_active = 1
             ORDER BY u.name"
        );
    }

    public function getAllPatients(): array {
        return $this->query(
            "SELECT id, CONCAT(first_name,' ',last_name) as full_name, phone
             FROM patients WHERE is_active=1 ORDER BY last_name, first_name"
        );
    }

    public function getAllProcedures(): array {
        return $this->query(
            "SELECT * FROM procedures WHERE is_active=1 ORDER BY name"
        );
    }

    public function createAppointment(array $data): int {
        return $this->insert($data);
    }

    public function updateAppointment(int $id, array $data): bool {
        return $this->update($id, $data);
    }

    public function cancelAppointment(int $id): bool {
        return $this->update($id, ['status' => 'cancelled']);
    }

    public function checkConflict(int $dentistId, string $date, string $start, string $end, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) as n FROM appointments
                WHERE dentist_id = ? AND date = ?
                  AND status != 'cancelled'
                  AND id != ?
                  AND (
                    (start_time < ? AND end_time > ?)
                  )";
        $row = $this->queryOne($sql, [$dentistId, $date, $excludeId, $end, $start]);
        return (int)($row['n'] ?? 0) > 0;
    }

    public function getWeekStats(string $startDate, string $endDate): array {
        $total = $this->queryOne(
            "SELECT COUNT(*) as n FROM appointments WHERE date BETWEEN ? AND ? AND status != 'cancelled'",
            [$startDate, $endDate]
        );
        $confirmed = $this->queryOne(
            "SELECT COUNT(*) as n FROM appointments WHERE date BETWEEN ? AND ? AND status = 'confirmed'",
            [$startDate, $endDate]
        );
        $completed = $this->queryOne(
            "SELECT COUNT(*) as n FROM appointments WHERE date BETWEEN ? AND ? AND status = 'completed'",
            [$startDate, $endDate]
        );
        return [
            'total'     => (int)$total['n'],
            'confirmed' => (int)$confirmed['n'],
            'completed' => (int)$completed['n'],
        ];
    }
}
