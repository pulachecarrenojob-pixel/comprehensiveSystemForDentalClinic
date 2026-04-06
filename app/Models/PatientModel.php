<?php
class PatientModel extends Model {
    protected string $table = 'patients';

    public function getAllWithInsurance(string $search = ''): array {
        $sql = "SELECT p.*, i.name as insurance_name
                FROM patients p
                LEFT JOIN insurance i ON p.insurance_id = i.id
                WHERE p.is_active = 1";
        $params = [];
        if ($search) {
            $sql .= " AND (p.first_name LIKE ? OR p.last_name LIKE ?
                      OR p.email LIKE ? OR p.phone LIKE ? OR p.id_number LIKE ?)";
            $like = "%{$search}%";
            $params = [$like, $like, $like, $like, $like];
        }
        $sql .= " ORDER BY p.last_name, p.first_name";
        return $this->query($sql, $params);
    }

    public function getById(int $id): array|false {
        return $this->queryOne(
            "SELECT p.*, i.name as insurance_name
             FROM patients p
             LEFT JOIN insurance i ON p.insurance_id = i.id
             WHERE p.id = ? AND p.is_active = 1",
            [$id]
        );
    }

    public function getStats(): array {
        $total = $this->queryOne("SELECT COUNT(*) as n FROM patients WHERE is_active=1");
        $newMonth = $this->queryOne(
            "SELECT COUNT(*) as n FROM patients
             WHERE is_active=1 AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())"
        );
        $withInsurance = $this->queryOne(
            "SELECT COUNT(*) as n FROM patients WHERE is_active=1 AND insurance_id IS NOT NULL"
        );
        return [
            'total'         => (int)$total['n'],
            'new_month'     => (int)$newMonth['n'],
            'with_insurance'=> (int)$withInsurance['n'],
        ];
    }

    public function getInsurancePlans(): array {
        return $this->query("SELECT * FROM insurance WHERE is_active=1 ORDER BY name");
    }

    public function getAppointmentHistory(int $patientId): array {
        return $this->query(
            "SELECT a.*, pr.name as procedure_name, pr.color as procedure_color,
                    u.name as dentist_name
             FROM appointments a
             JOIN procedures pr ON a.procedure_id = pr.id
             JOIN dentists d    ON a.dentist_id = d.id
             JOIN users u       ON d.user_id = u.id
             WHERE a.patient_id = ?
             ORDER BY a.date DESC, a.start_time DESC
             LIMIT 20",
            [$patientId]
        );
    }

    public function getAnamnesisSummary(int $patientId): array|false {
        return $this->queryOne(
            "SELECT a.*,
                    GROUP_CONCAT(DISTINCT al.name ORDER BY al.name SEPARATOR ', ') as allergies_list,
                    GROUP_CONCAT(DISTINCT mc.name ORDER BY mc.name SEPARATOR ', ') as conditions_list
             FROM anamnesis a
             LEFT JOIN allergies al       ON al.anamnesis_id = a.id
             LEFT JOIN medical_conditions mc ON mc.anamnesis_id = a.id
             WHERE a.patient_id = ?
             ORDER BY a.created_at DESC LIMIT 1",
            [$patientId]
        );
    }

    public function createPatient(array $data): int {
        return $this->insert($data);
    }

    public function updatePatient(int $id, array $data): bool {
        return $this->update($id, $data);
    }

    public function softDelete(int $id): bool {
        return $this->update($id, ['is_active' => 0]);
    }

    public function searchJson(string $q): array {
        return $this->getAllWithInsurance($q);
    }

    public function getAge(string $birthDate): int {
        return (int)date_diff(date_create($birthDate), date_create('today'))->y;
    }
}
