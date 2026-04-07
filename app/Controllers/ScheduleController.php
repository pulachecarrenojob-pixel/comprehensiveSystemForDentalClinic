<?php
class ScheduleController extends Controller {

    private ScheduleModel $model;

    public function __construct() {
        $this->model = new ScheduleModel();
    }

    public function index(): void {
        Auth::check();

        // Determine week start (Monday)
        $weekOffset = (int)$this->input('week', 0);
        $monday = new DateTime();
        $monday->modify('monday this week');
        $monday->modify("{$weekOffset} week");
        $sunday = clone $monday;
        $sunday->modify('+6 days');

        $startDate   = $monday->format('Y-m-d');
        $endDate     = $sunday->format('Y-m-d');
        $appointments= $this->model->getWeekAppointments($startDate, $endDate);
        $dentists    = $this->model->getAllDentists();
        $patients    = $this->model->getAllPatients();
        $procedures  = $this->model->getAllProcedures();
        $stats       = $this->model->getWeekStats($startDate, $endDate);

        // Build week days array
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $d = clone $monday;
            $d->modify("+{$i} days");
            $days[] = [
                'date'      => $d->format('Y-m-d'),
                'label'     => $d->format('D'),
                'day'       => $d->format('d'),
                'month'     => $d->format('M'),
                'isToday'   => $d->format('Y-m-d') === date('Y-m-d'),
                'isWeekend' => in_array($d->format('N'), ['6','7']),
            ];
        }

        $this->view('schedule/index', [
            'title'        => 'Schedule',
            'appointments' => $appointments,
            'dentists'     => $dentists,
            'patients'     => $patients,
            'procedures'   => $procedures,
            'days'         => $days,
            'stats'        => $stats,
            'weekOffset'   => $weekOffset,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'monday'       => $monday,
            'sunday'       => $sunday,
            'extraJs'      => ['schedule.js'],
            'extraCss'     => ['schedule.css'],
        ]);
    }

    public function store(): void {
        Auth::check();
        Auth::verifyCsrf();

        $dentistId   = (int)$this->input('dentist_id');
        $patientId   = (int)$this->input('patient_id');
        $procedureId = (int)$this->input('procedure_id');
        $date        = $this->sanitize($this->input('date'));
        $startTime   = $this->sanitize($this->input('start_time'));
        $endTime     = $this->sanitize($this->input('end_time'));
        $notes       = $this->sanitize($this->input('notes'));
        $weekOffset  = (int)$this->input('week_offset', 0);

        if (!$dentistId || !$patientId || !$procedureId || !$date || !$startTime || !$endTime) {
            flash('error', 'All fields are required.');
            $this->redirect('/schedule?week=' . $weekOffset);
        }

        if ($this->model->checkConflict($dentistId, $date, $startTime, $endTime)) {
            flash('error', 'The dentist already has an appointment at that time.');
            $this->redirect('/schedule?week=' . $weekOffset);
        }

        $this->model->createAppointment([
            'patient_id'   => $patientId,
            'dentist_id'   => $dentistId,
            'procedure_id' => $procedureId,
            'date'         => $date,
            'start_time'   => $startTime,
            'end_time'     => $endTime,
            'status'       => 'scheduled',
            'notes'        => $notes,
            'created_by'   => Auth::id(),
        ]);

        flash('success', 'Appointment scheduled successfully.');
        $this->redirect('/schedule?week=' . $weekOffset);
    }

    public function update(): void {
        Auth::check();
        Auth::verifyCsrf();

        $id     = (int)$this->input('id');
        $status = $this->sanitize($this->input('status'));
        $notes  = $this->sanitize($this->input('notes'));
        $week   = (int)$this->input('week_offset', 0);

        $this->model->updateAppointment($id, [
            'status' => $status,
            'notes'  => $notes,
        ]);

        flash('success', 'Appointment updated successfully.');
        $this->redirect('/schedule?week=' . $week);
    }

    public function delete(): void {
        Auth::check();
        Auth::verifyCsrf();
        $id   = (int)$this->input('id');
        $week = (int)$this->input('week_offset', 0);
        $this->model->cancelAppointment($id);
        flash('success', 'Appointment cancelled.');
        $this->redirect('/schedule?week=' . $week);
    }
}
