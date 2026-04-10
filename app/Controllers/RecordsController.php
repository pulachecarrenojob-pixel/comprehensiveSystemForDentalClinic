<?php
class RecordsController extends Controller {

    private RecordModel $model;

    public function __construct() {
        $this->model = new RecordModel();
    }

    public function index(): void {
        Auth::check();
        $search    = $this->sanitize($this->input('search', ''));
        $patientId = (int)$this->input('patient_id', 0);
        $records   = $this->model->getAllWithDetails($search, $patientId);
        $stats     = $this->model->getStats();
        $patients  = $this->model->getAllPatients();

        $this->view('records/index', [
            'title'     => 'Clinical Records',
            'records'   => $records,
            'stats'     => $stats,
            'patients'  => $patients,
            'search'    => $search,
            'patientId' => $patientId,
            'extraJs'   => ['records.js'],
            'extraCss'  => ['records.css'],
        ]);
    }

    public function create(): void {
        Auth::check();
        $patientId  = (int)$this->input('patient_id', 0);
        $patients   = $this->model->getAllPatients();
        $dentists   = $this->model->getAllDentists();
        $procedures = $this->model->getAllProcedures();

        // Pre-load appointments if patient selected
        $appointments = $patientId
            ? $this->model->getAppointmentsForPatient($patientId)
            : [];

        $this->view('records/form', [
            'title'        => 'New Clinical Record',
            'record'       => null,
            'patients'     => $patients,
            'dentists'     => $dentists,
            'procedures'   => $procedures,
            'appointments' => $appointments,
            'preselect'    => $patientId,
            'isEdit'       => false,
            'extraJs'      => ['records.js'],
            'extraCss'     => ['records.css'],
        ]);
    }

    public function store(): void {
        Auth::check();
        Auth::verifyCsrf();

        $patientId     = (int)$this->input('patient_id');
        $dentistId     = (int)$this->input('dentist_id');
        $procedureId   = (int)$this->input('procedure_id');
        $appointmentId = (int)$this->input('appointment_id') ?: null;

        if (!$patientId || !$dentistId || !$procedureId) {
            flash('error', 'Patient, dentist and procedure are required.');
            $this->redirect('/records/create');
        }

        $data = [
            'appointment_id' => $appointmentId,
            'patient_id'     => $patientId,
            'dentist_id'     => $dentistId,
            'procedure_id'   => $procedureId,
            'teeth'          => $this->sanitize($this->input('teeth', '')),
            'description'    => $this->sanitize($this->input('description')),
            'observations'   => $this->sanitize($this->input('observations', '')),
            'duration'       => (int)$this->input('duration', 0) ?: null,
        ];

        if (empty($data['description'])) {
            flash('error', 'Description is required.');
            $this->redirect('/records/create');
        }

        $id = $this->model->createRecord($data);
        flash('success', 'Clinical record created successfully.');
        $this->redirect('/records/show?id=' . $id);
    }

    public function show(): void {
        Auth::check();
        $id     = (int)$this->input('id');
        $record = $this->model->getById($id);

        if (!$record) {
            flash('error', 'Record not found.');
            $this->redirect('/records');
        }

        $this->view('records/show', [
            'title'      => 'Record — ' . ($record['patient_name'] ?? ''),
            'record'     => $record,
            'extraCss'   => ['records.css'],
        ]);
    }

    public function edit(): void {
        Auth::check();
        $id     = (int)$this->input('id');
        $record = $this->model->getById($id);

        if (!$record) {
            flash('error', 'Record not found.');
            $this->redirect('/records');
        }

        $patients   = $this->model->getAllPatients();
        $dentists   = $this->model->getAllDentists();
        $procedures = $this->model->getAllProcedures();

        $this->view('records/form', [
            'title'        => 'Edit Record',
            'record'       => $record,
            'patients'     => $patients,
            'dentists'     => $dentists,
            'procedures'   => $procedures,
            'appointments' => [],
            'preselect'    => 0,
            'isEdit'       => true,
            'extraJs'      => ['records.js'],
            'extraCss'     => ['records.css'],
        ]);
    }

    public function update(): void {
        Auth::check();
        Auth::verifyCsrf();

        $id   = (int)$this->input('id');
        $data = [
            'procedure_id' => (int)$this->input('procedure_id'),
            'dentist_id'   => (int)$this->input('dentist_id'),
            'teeth'        => $this->sanitize($this->input('teeth', '')),
            'description'  => $this->sanitize($this->input('description')),
            'observations' => $this->sanitize($this->input('observations', '')),
            'duration'     => (int)$this->input('duration', 0) ?: null,
        ];

        $this->model->updateRecord($id, $data);
        flash('success', 'Record updated successfully.');
        $this->redirect('/records/show?id=' . $id);
    }
}
