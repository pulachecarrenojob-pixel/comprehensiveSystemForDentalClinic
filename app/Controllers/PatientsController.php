<?php
class PatientsController extends Controller {

    private PatientModel $model;

    public function __construct() {
        $this->model = new PatientModel();
    }

    public function index(): void {
        Auth::check();
        $search   = $this->sanitize($this->input('search', ''));
        $patients = $this->model->getAllWithInsurance($search);
        $stats    = $this->model->getStats();

        $this->view('patients/index', [
            'title'    => 'Patients',
            'patients' => $patients,
            'stats'    => $stats,
            'search'   => $search,
            'extraJs'  => ['patients.js'],
        ]);
    }

    public function create(): void {
        Auth::check();
        $insurance = $this->model->getInsurancePlans();
        $this->view('patients/create', [
            'title'     => 'New Patient',
            'insurance' => $insurance,
        ]);
    }

    public function store(): void {
        Auth::check();
        Auth::verifyCsrf();

        $data = [
            'first_name'   => $this->sanitize($this->input('first_name')),
            'last_name'    => $this->sanitize($this->input('last_name')),
            'email'        => $this->sanitize($this->input('email')),
            'phone'        => $this->sanitize($this->input('phone')),
            'birth_date'   => $this->sanitize($this->input('birth_date')),
            'gender'       => $this->sanitize($this->input('gender', 'other')),
            'id_number'    => $this->sanitize($this->input('id_number')),
            'address'      => $this->sanitize($this->input('address')),
            'insurance_id' => $this->input('insurance_id') ?: null,
            'notes'        => $this->sanitize($this->input('notes')),
        ];

        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['phone'])) {
            flash('error', 'First name, last name and phone are required.');
            $this->redirect('/patients/create');
        }

        $id = $this->model->createPatient($data);
        flash('success', 'Patient ' . $data['first_name'] . ' ' . $data['last_name'] . ' created successfully.');
        $this->redirect('/patients/show?id=' . $id);
    }

    public function show(): void {
        Auth::check();
        $id      = (int)$this->input('id');
        $patient = $this->model->getById($id);

        if (!$patient) {
            flash('error', 'Patient not found.');
            $this->redirect('/patients');
        }

        $appointments = $this->model->getAppointmentHistory($id);
        $anamnesis    = $this->model->getAnamnesisSummary($id);

        $this->view('patients/show', [
            'title'        => $patient['first_name'] . ' ' . $patient['last_name'],
            'patient'      => $patient,
            'appointments' => $appointments,
            'anamnesis'    => $anamnesis,
        ]);
    }

    public function edit(): void {
        Auth::check();
        $id      = (int)$this->input('id');
        $patient = $this->model->getById($id);

        if (!$patient) {
            flash('error', 'Patient not found.');
            $this->redirect('/patients');
        }

        $insurance = $this->model->getInsurancePlans();
        $this->view('patients/edit', [
            'title'     => 'Edit Patient',
            'patient'   => $patient,
            'insurance' => $insurance,
        ]);
    }

    public function update(): void {
        Auth::check();
        Auth::verifyCsrf();

        $id = (int)$this->input('id');
        $data = [
            'first_name'   => $this->sanitize($this->input('first_name')),
            'last_name'    => $this->sanitize($this->input('last_name')),
            'email'        => $this->sanitize($this->input('email')),
            'phone'        => $this->sanitize($this->input('phone')),
            'birth_date'   => $this->sanitize($this->input('birth_date')),
            'gender'       => $this->sanitize($this->input('gender', 'other')),
            'id_number'    => $this->sanitize($this->input('id_number')),
            'address'      => $this->sanitize($this->input('address')),
            'insurance_id' => $this->input('insurance_id') ?: null,
            'notes'        => $this->sanitize($this->input('notes')),
        ];

        $this->model->updatePatient($id, $data);
        flash('success', 'Patient updated successfully.');
        $this->redirect('/patients/show?id=' . $id);
    }

    public function delete(): void {
        Auth::check();
        Auth::verifyCsrf();
        $id = (int)$this->input('id');
        $this->model->softDelete($id);
        flash('success', 'Patient removed successfully.');
        $this->redirect('/patients');
    }

    public function search(): void {
        Auth::check();
        $q       = $this->sanitize($this->input('q', ''));
        $results = $this->model->searchJson($q);
        $this->json($results);
    }
}
