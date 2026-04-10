<?php
class AnamnesisModel extends Model {
    protected string $table = 'anamnesis';

    public function getAllWithPatient(): array {
        return $this->query(
            "SELECT a.*,
                    CONCAT(p.first_name,' ',p.last_name) as patient_name,
                    p.phone as patient_phone,
                    u.name as created_by_name,
                    GROUP_CONCAT(DISTINCT al.name ORDER BY al.name SEPARATOR '|||') as allergies_raw,
                    GROUP_CONCAT(DISTINCT mc.name ORDER BY mc.name SEPARATOR '|||') as conditions_raw
             FROM anamnesis a
             JOIN patients p ON a.patient_id = p.id
             JOIN users u    ON a.created_by  = u.id
             LEFT JOIN allergies al           ON al.anamnesis_id = a.id
             LEFT JOIN medical_conditions mc  ON mc.anamnesis_id = a.id
             GROUP BY a.id
             ORDER BY a.created_at DESC"
        );
    }

    public function getById(int $id): array|false {
        return $this->queryOne(
            "SELECT a.*,
                    CONCAT(p.first_name,' ',p.last_name) as patient_name,
                    p.birth_date, p.gender, p.phone as patient_phone,
                    u.name as created_by_name
             FROM anamnesis a
             JOIN patients p ON a.patient_id = p.id
             JOIN users u    ON a.created_by  = u.id
             WHERE a.id = ?",
            [$id]
        );
    }

    public function getByPatient(int $patientId): array|false {
        return $this->queryOne(
            "SELECT * FROM anamnesis WHERE patient_id = ? ORDER BY created_at DESC LIMIT 1",
            [$patientId]
        );
    }

    public function getAllergies(int $anamnesisId): array {
        return $this->query(
            "SELECT * FROM allergies WHERE anamnesis_id = ? ORDER BY name",
            [$anamnesisId]
        );
    }

    public function getConditions(int $anamnesisId): array {
        return $this->query(
            "SELECT * FROM medical_conditions WHERE anamnesis_id = ? ORDER BY name",
            [$anamnesisId]
        );
    }

    public function getAllPatients(): array {
        return $this->query(
            "SELECT p.id, CONCAT(p.first_name,' ',p.last_name) as full_name
             FROM patients p
             WHERE p.is_active = 1
             ORDER BY p.last_name, p.first_name"
        );
    }

    public function create(array $data, array $allergies, array $conditions): int {
        $id = $this->insert($data);
        $this->saveAllergies($id, $allergies);
        $this->saveConditions($id, $conditions);
        return $id;
    }

    public function saveAllergies(int $anamnesisId, array $allergies): void {
        $this->execute("DELETE FROM allergies WHERE anamnesis_id = ?", [$anamnesisId]);
        foreach ($allergies as $a) {
            $name = trim($a['name'] ?? '');
            if (!$name) continue;
            $this->execute(
                "INSERT INTO allergies (anamnesis_id, name, severity) VALUES (?, ?, ?)",
                [$anamnesisId, $name, $a['severity'] ?? 'mild']
            );
        }
    }

    public function saveConditions(int $anamnesisId, array $conditions): void {
        $this->execute("DELETE FROM medical_conditions WHERE anamnesis_id = ?", [$anamnesisId]);
        foreach ($conditions as $name) {
            $name = trim($name);
            if (!$name) continue;
            $this->execute(
                "INSERT INTO medical_conditions (anamnesis_id, name) VALUES (?, ?)",
                [$anamnesisId, $name]
            );
        }
    }

    public function updateAnamnesis(int $id, array $data, array $allergies, array $conditions): bool {
        $ok = parent::update($id, $data);
        $this->saveAllergies($id, $allergies);
        $this->saveConditions($id, $conditions);
        return $ok;
    }

    public function getStats(): array {
        $total = $this->queryOne("SELECT COUNT(*) as n FROM anamnesis");
        $withAllergies = $this->queryOne(
            "SELECT COUNT(DISTINCT anamnesis_id) as n FROM allergies"
        );
        $thisMonth = $this->queryOne(
            "SELECT COUNT(*) as n FROM anamnesis
             WHERE MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())"
        );
        return [
            'total'         => (int)($total['n'] ?? 0),
            'with_allergies'=> (int)($withAllergies['n'] ?? 0),
            'this_month'    => (int)($thisMonth['n'] ?? 0),
        ];
    }

    /** Parse pipe-separated string into array */
    public static function parsePiped(?string $raw): array {
        if (!$raw) return [];
        return array_filter(array_map('trim', explode('|||', $raw)));
    }
}