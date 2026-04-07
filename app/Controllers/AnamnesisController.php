<?php
class AnamnesisController extends Controller {

    private AnamnesisModel $model;

    public function __construct() {
        $this->model = new AnamnesisModel();
    }

    public function index(): void {
        Auth::check();
        $list  = $this->model->getAllWithPatient();
        $stats = $this->model->getStats();

        $this->view('anamnesis/index', [
            'title'  => 'Anamnesis',
            'list'   => $list,
            'stats'  => $stats,
            'extraJs'  => ['anamnesis.js'],
            'extraCss' => ['anamnesis.css'],
        ]);
    }

    public function create(): void {
        Auth::check();
        $patientId = (int)$this->input('patient_id', 0);
        $patients  = $this->model->getAllPatients();

        $this->view('anamnesis/form', [
            'title'     => 'New Anamnesis',
            'patients'  => $patients,
            'anamnesis' => null,
            'allergies' => [],
            'conditions'=> [],
            'preselect' => $patientId,
            'extraJs'   => ['anamnesis.js'],
            'extraCss'  => ['anamnesis.css'],
        ]);
    }

    public function store(): void {
        Auth::check();
        Auth::verifyCsrf();

        $patientId = (int)$this->input('patient_id');
        if (!$patientId) {
            flash('error', 'Please select a patient.');
            $this->redirect('/anamnesis/create');
        }

        $data = [
            'patient_id'     => $patientId,
            'chief_complaint'=> $this->sanitize($this->input('chief_complaint')),
            'medical_history'=> $this->sanitize($this->input('medical_history')),
            'current_meds'   => $this->sanitize($this->input('current_meds')),
            'smoker'         => $this->input('smoker') ? 1 : 0,
            'pregnant'       => $this->input('pregnant') ? 1 : 0,
            'blood_type'     => $this->sanitize($this->input('blood_type')),
            'notes'          => $this->sanitize($this->input('notes')),
            'created_by'     => Auth::id(),
        ];

        $allergies  = $this->parseAllergies();
        $conditions = $this->parseConditions();

        $id = $this->model->create($data, $allergies, $conditions);
        flash('success', 'Anamnesis created successfully.');
        $this->redirect('/anamnesis/show?id=' . $id);
    }

    public function show(): void {
        Auth::check();
        $id        = (int)$this->input('id');
        $anamnesis = $this->model->getById($id);

        if (!$anamnesis) {
            flash('error', 'Anamnesis not found.');
            $this->redirect('/anamnesis');
        }

        $allergies  = $this->model->getAllergies($id);
        $conditions = $this->model->getConditions($id);

        $this->view('anamnesis/show', [
            'title'      => 'Anamnesis — ' . ($anamnesis['patient_name'] ?? ''),
            'anamnesis'  => $anamnesis,
            'allergies'  => $allergies,
            'conditions' => $conditions,
            'extraCss'   => ['anamnesis.css'],
        ]);
    }

    public function edit(): void {
        Auth::check();
        $id        = (int)$this->input('id');
        $anamnesis = $this->model->getById($id);

        if (!$anamnesis) {
            flash('error', 'Anamnesis not found.');
            $this->redirect('/anamnesis');
        }

        $patients   = $this->model->getAllPatients();
        $allergies  = $this->model->getAllergies($id);
        $conditions = $this->model->getConditions($id);

        $this->view('anamnesis/form', [
            'title'      => 'Edit Anamnesis',
            'patients'   => $patients,
            'anamnesis'  => $anamnesis,
            'allergies'  => $allergies,
            'conditions' => $conditions,
            'preselect'  => 0,
            'extraJs'    => ['anamnesis.js'],
            'extraCss'   => ['anamnesis.css'],
        ]);
    }

    public function update(): void {
        Auth::check();
        Auth::verifyCsrf();

        $id   = (int)$this->input('id');
        $data = [
            'chief_complaint'=> $this->sanitize($this->input('chief_complaint')),
            'medical_history'=> $this->sanitize($this->input('medical_history')),
            'current_meds'   => $this->sanitize($this->input('current_meds')),
            'smoker'         => $this->input('smoker') ? 1 : 0,
            'pregnant'       => $this->input('pregnant') ? 1 : 0,
            'blood_type'     => $this->sanitize($this->input('blood_type')),
            'notes'          => $this->sanitize($this->input('notes')),
        ];

        $this->model->update($id, $data, $this->parseAllergies(), $this->parseConditions());
        flash('success', 'Anamnesis updated successfully.');
        $this->redirect('/anamnesis/show?id=' . $id);
    }

    // ---- Private helpers ----

    private function parseAllergies(): array {
        $names      = $_POST['allergy_name']     ?? [];
        $severities = $_POST['allergy_severity'] ?? [];
        $result = [];
        foreach ($names as $i => $name) {
            if (trim($name)) {
                $result[] = [
                    'name'     => trim($name),
                    'severity' => $severities[$i] ?? 'mild',
                ];
            }
        }
        return $result;
    }

    private function parseConditions(): array {
        return array_filter(
            array_map('trim', $_POST['conditions'] ?? [])
        );
    }
}
