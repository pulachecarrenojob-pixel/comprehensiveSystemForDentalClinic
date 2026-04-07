<?php
// Group appointments by dentist and date
$apptMap = [];
foreach ($appointments as $a) {
    $apptMap[$a['dentist_id']][$a['date']][] = $a;
}

// Time slots: 08:00 to 19:00 every 30 min
$slots = [];
for ($h = 8; $h < 19; $h++) {
    $slots[] = sprintf('%02d:00', $h);
    $slots[] = sprintf('%02d:30', $h);
}

$prevWeek = $weekOffset - 1;
$nextWeek = $weekOffset + 1;
?>

<!-- Header -->
<div class="page-header">
  <div class="page-header-left">
    <h2>Schedule</h2>
    <p><?= $monday->format('d M') ?> — <?= $sunday->format('d M Y') ?></p>
  </div>
  <button class="btn btn-primary" onclick="openModal('newApptModal')">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Appointment
  </button>
</div>

<!-- Week stats -->
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px">
  <div class="kpi-card" style="padding:14px 18px">
    <div class="kpi-left">
      <div class="kpi-icon blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
      <div><div class="kpi-label">This Week</div><div class="kpi-value" style="font-size:1.4rem"><?= $stats['total'] ?></div></div>
    </div>
  </div>
  <div class="kpi-card" style="padding:14px 18px">
    <div class="kpi-left">
      <div class="kpi-icon green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
      <div><div class="kpi-label">Confirmed</div><div class="kpi-value" style="font-size:1.4rem"><?= $stats['confirmed'] ?></div></div>
    </div>
  </div>
  <div class="kpi-card" style="padding:14px 18px">
    <div class="kpi-left">
      <div class="kpi-icon orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
      <div><div class="kpi-label">Completed</div><div class="kpi-value" style="font-size:1.4rem"><?= $stats['completed'] ?></div></div>
    </div>
  </div>
</div>

<!-- Calendar controls -->
<div class="card" style="margin-bottom:20px">
  <div class="card-header" style="gap:12px;flex-wrap:wrap">
    <div style="display:flex;align-items:center;gap:8px">
      <a href="?url=schedule&week=<?= $prevWeek ?>" class="cal-nav-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      </a>
      <a href="?url=schedule&week=0" class="btn btn-outline" style="padding:6px 14px;font-size:0.8rem">Today</a>
      <a href="?url=schedule&week=<?= $nextWeek ?>" class="cal-nav-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
      <span style="font-size:0.9rem;font-weight:500;margin-left:4px">
        <?= $monday->format('d M') ?> – <?= $sunday->format('d M Y') ?>
      </span>
    </div>

    <!-- Dentist filter -->
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
      <span style="font-size:0.78rem;color:var(--text-muted)">Dentists:</span>
      <button class="dentist-filter-btn active" data-dentist="all">All</button>
      <?php foreach($dentists as $d): ?>
      <button class="dentist-filter-btn" data-dentist="<?= $d['id'] ?>" style="--dc:<?= clean($d['color']) ?>">
        <span class="dentist-dot" style="background:<?= clean($d['color']) ?>"></span>
        <?= clean($d['name']) ?>
      </button>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Weekly Calendar Grid -->
<div class="cal-wrap card">
  <div class="cal-grid" id="calGrid" style="--dentist-cols:<?= count($dentists) ?>">

    <!-- Corner -->
    <div class="cal-corner">
      <span style="font-size:0.72rem;color:var(--text-muted)">TIME</span>
    </div>

    <!-- Day headers -->
    <?php foreach($days as $day): ?>
    <div class="cal-day-header <?= $day['isToday'] ? 'today' : '' ?> <?= $day['isWeekend'] ? 'weekend' : '' ?>">
      <span class="cal-day-label"><?= $day['label'] ?></span>
      <span class="cal-day-num <?= $day['isToday'] ? 'today-num' : '' ?>"><?= $day['day'] ?></span>
      <span class="cal-day-month"><?= $day['month'] ?></span>
    </div>
    <?php endforeach; ?>

    <!-- Time slots + appointments -->
    <?php foreach($slots as $slot): ?>
    <!-- Time label -->
    <div class="cal-time">
      <?php if(substr($slot, 3) === '00'): ?>
      <span><?= $slot ?></span>
      <?php endif; ?>
    </div>

    <!-- Day cells -->
    <?php foreach($days as $day): ?>
    <div class="cal-cell <?= $day['isToday'] ? 'today-col' : '' ?> <?= $day['isWeekend'] ? 'weekend-col' : '' ?>"
         data-date="<?= $day['date'] ?>"
         data-time="<?= $slot ?>"
         onclick="quickBook('<?= $day['date'] ?>', '<?= $slot ?>')">

      <?php
      // Render appointments that start in this slot
      foreach($dentists as $dentist):
        $dayAppts = $apptMap[$dentist['id']][$day['date']] ?? [];
        foreach($dayAppts as $appt):
          if(substr($appt['start_time'], 0, 5) !== $slot): continue; endif;

          // Calculate height based on duration
          $startMin = (int)substr($appt['start_time'],0,2)*60 + (int)substr($appt['start_time'],3,2);
          $endMin   = (int)substr($appt['end_time'],0,2)*60   + (int)substr($appt['end_time'],3,2);
          $duration = max(30, $endMin - $startMin);
          $slots30  = $duration / 30;
      ?>
      <div class="cal-appt"
           style="height:calc(<?= $slots30 ?> * var(--slot-h) - 4px);border-left-color:<?= clean($appt['dentist_color']) ?>;background:<?= clean($appt['dentist_color']) ?>18"
           onclick="event.stopPropagation();viewAppt(<?= htmlspecialchars(json_encode($appt), ENT_QUOTES) ?>)"
           data-dentist="<?= $appt['dentist_id'] ?>">
        <div class="cal-appt-time"><?= substr($appt['start_time'],0,5) ?> – <?= substr($appt['end_time'],0,5) ?></div>
        <div class="cal-appt-patient"><?= clean($appt['patient_name']) ?></div>
        <div class="cal-appt-proc" style="color:<?= clean($appt['procedure_color']) ?>"><?= clean($appt['procedure_name']) ?></div>
        <?= statusBadge($appt['status']) ?>
      </div>
      <?php endforeach; endforeach; ?>

    </div>
    <?php endforeach; ?>
    <?php endforeach; ?>

  </div>
</div>

<!-- ============================================================
     NEW APPOINTMENT MODAL
     ============================================================ -->
<div class="modal-backdrop" id="newApptModal">
  <div class="modal" style="max-width:560px">
    <div class="modal-header">
      <span class="modal-title">New Appointment</span>
      <button class="modal-close" onclick="closeModal('newApptModal')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="<?= url('schedule/store') ?>">
      <?= csrfField() ?>
      <input type="hidden" name="week_offset" value="<?= $weekOffset ?>">
      <div class="modal-body">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Patient <span style="color:var(--danger)">*</span></label>
            <select name="patient_id" id="apptPatient" class="form-select" required>
              <option value="">Select patient...</option>
              <?php foreach($patients as $p): ?>
              <option value="<?= $p['id'] ?>"><?= clean($p['full_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Dentist <span style="color:var(--danger)">*</span></label>
            <select name="dentist_id" id="apptDentist" class="form-select" required>
              <option value="">Select dentist...</option>
              <?php foreach($dentists as $d): ?>
              <option value="<?= $d['id'] ?>"><?= clean($d['name']) ?> — <?= clean($d['specialty']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Procedure <span style="color:var(--danger)">*</span></label>
          <select name="procedure_id" id="apptProcedure" class="form-select" required onchange="updateEndTime()">
            <option value="">Select procedure...</option>
            <?php foreach($procedures as $p): ?>
            <option value="<?= $p['id'] ?>" data-duration="<?= $p['duration'] ?>">
              <?= clean($p['name']) ?> (<?= $p['duration'] ?> min — <?= formatMoney($p['price']) ?>)
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-row-3">
          <div class="form-group">
            <label class="form-label">Date <span style="color:var(--danger)">*</span></label>
            <input type="date" name="date" id="apptDate" class="form-input" required
              value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Start time <span style="color:var(--danger)">*</span></label>
            <input type="time" name="start_time" id="apptStart" class="form-input" required
              value="09:00" step="1800" onchange="updateEndTime()">
          </div>
          <div class="form-group">
            <label class="form-label">End time <span style="color:var(--danger)">*</span></label>
            <input type="time" name="end_time" id="apptEnd" class="form-input" required
              value="10:00" step="1800">
          </div>
        </div>

        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-textarea" rows="3" placeholder="Additional notes..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('newApptModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Book Appointment
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ============================================================
     VIEW / EDIT APPOINTMENT MODAL
     ============================================================ -->
<div class="modal-backdrop" id="viewApptModal">
  <div class="modal" style="max-width:480px">
    <div class="modal-header">
      <span class="modal-title" id="vApptTitle">Appointment Details</span>
      <button class="modal-close" onclick="closeModal('viewApptModal')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal-body" id="vApptBody"></div>
    <div class="modal-footer">
      <form method="POST" action="<?= url('schedule/delete') ?>" id="cancelApptForm" style="margin-right:auto">
        <?= csrfField() ?>
        <input type="hidden" name="id" id="vApptCancelId">
        <input type="hidden" name="week_offset" value="<?= $weekOffset ?>">
        <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this appointment?')">
          Cancel Appointment
        </button>
      </form>
      <form method="POST" action="<?= url('schedule/update') ?>" id="updateApptForm">
        <?= csrfField() ?>
        <input type="hidden" name="id" id="vApptId">
        <input type="hidden" name="week_offset" value="<?= $weekOffset ?>">
        <select name="status" id="vApptStatus" class="form-select" style="width:auto;display:inline-block;margin-right:8px">
          <option value="scheduled">Scheduled</option>
          <option value="confirmed">Confirmed</option>
          <option value="completed">Completed</option>
          <option value="no_show">No Show</option>
        </select>
        <button type="submit" class="btn btn-primary">Update</button>
      </form>
    </div>
  </div>
</div>

<input type="hidden" id="weekOffset" value="<?= $weekOffset ?>">
