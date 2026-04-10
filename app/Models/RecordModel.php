<?php
class RecordModel extends Model {
    protected string $table = 'clinical_records';

    public function getAllWithDetails(string $search = '', int $patientId = 0): array {
        $sql = "SELECT cr.*,
                    CONCAT(p.first_name,' ',p.last_name) as patient_name,
                    p.phone as patient_phone,
                    u.name as dentist_name,
                    d.specialty as dentist_specialty,
                    d.color as dentist_color,
                    pr.name as procedure_name,
                    pr.color as procedure_color,
                    pr.category as procedure_category
                FROM clinical_records cr
                JOIN patients p    ON cr.patient_id   = p.id
                JOIN dentists d    ON cr.dentist_id    = d.id
                JOIN users u       ON d.user_id        = u.id
                JOIN procedures pr ON cr.procedure_id  = pr.id
                WHERE 1=1";
        $params = [];

        if ($search) {
            $sql .= " AND (p.first_name LIKE ? OR p.last_name LIKE ? OR pr.name LIKE ?)";
            $like = "%{$search}%";
            $params = array_merge($params, [$like, $like, $like]);
        }

        if ($patientId) {
            $sql .= " AND cr.patient_id = ?";
            $params[] = $patientId;
        }

        $sql .= " ORDER BY cr.created_at DESC";
        return $this->query($sql, $params);
    }

    public function getById(int $id): array|false {
        return $this->queryOne(
            "SELECT cr.*,
                    CONCAT(p.first_name,' ',p.last_name) as patient_name,
                    p.phone as patient_phone,
                    p.birth_date as patient_birth,
                    u.name as dentist_name,
                    d.specialty as dentist_specialty,
                    d.color as dentist_color,
                    pr.name as procedure_name,
                    pr.color as procedure_color,
                    pr.category as procedure_category,
                    pr.price as procedure_price,
                    a.date as appointment_date,
                    a.start_time, a.end_time
             FROM clinical_records cr
             JOIN patients p    ON cr.patient_id   = p.id
             JOIN dentists d    ON cr.dentist_id    = d.id
             JOIN users u       ON d.user_id        = u.id
             JOIN procedures pr ON cr.procedure_id  = pr.id
             LEFT JOIN appointments a ON cr.appointment_id = a.id
             WHERE cr.id = ?",
            [$id]
        );
    }

    public function getAllPatients(): array {
        return $this->query(
            "SELECT id, CONCAT(first_name,' ',last_name) as full_name
             FROM patients WHERE is_active=1 ORDER BY last_name, first_name"
        );
    }

    public function getAllDentists(): array {
        return $this->query(
            "SELECT d.id, u.name, d.specialty, d.color
             FROM dentists d JOIN users u ON d.user_id=u.id
             WHERE d.is_active=1 ORDER BY u.name"
        );
    }

    public function getAllProcedures(): array {
        return $this->query(
            "SELECT * FROM procedures WHERE is_active=1 ORDER BY category, name"
        );
    }

    public function getAppointmentsForPatient(int $patientId): array {
        return $this->query(
            "SELECT a.id, a.date, a.start_time,
                    pr.name as procedure_name,
                    u.name as dentist_name
             FROM appointments a
             JOIN procedures pr ON a.procedure_id = pr.id
             JOIN dentists d    ON a.dentist_id   = d.id
             JOIN users u       ON d.user_id      = u.id
             WHERE a.patient_id = ?
               AND a.status IN ('confirmed','completed')
               AND a.id NOT IN (SELECT appointment_id FROM clinical_records WHERE appointment_id IS NOT NULL)
             ORDER BY a.date DESC",
            [$patientId]
        );
    }

    public function getStats(): array {
        $total = $this->queryOne("SELECT COUNT(*) as n FROM clinical_records");
        $thisMonth = $this->queryOne(
            "SELECT COUNT(*) as n FROM clinical_records
             WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())"
        );
        $topProc = $this->queryOne(
            "SELECT pr.name, COUNT(*) as n
             FROM clinical_records cr
             JOIN procedures pr ON cr.procedure_id=pr.id
             GROUP BY pr.id ORDER BY n DESC LIMIT 1"
        );
        return [
            'total'      => (int)($total['n'] ?? 0),
            'this_month' => (int)($thisMonth['n'] ?? 0),
            'top_proc'   => $topProc['name'] ?? '—',
        ];
    }

    public function createRecord(array $data): int {
        // Mark appointment as completed
        if (!empty($data['appointment_id'])) {
            $this->execute(
                "UPDATE appointments SET status='completed' WHERE id=?",
                [$data['appointment_id']]
            );
        }
        return $this->insert($data);
    }

    public function updateRecord(int $id, array $data): bool {
        return $this->update($id, $data);
    }
}
