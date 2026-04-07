<?php
$age = $patient['birth_date']
    ? (int)date_diff(date_create($patient['birth_date']), date_create('today'))->y
    : null;
$genderColor = match($patient['gender'] ?? 'other') {
    'female' => '#D4537E',
    'male'   => '#378ADD',
    default  => '#888780'
};

// Safe clean helper — handles NULL values
function sc(?string $v): string { return $v ? clean($v) : '—'; }
?>

<div class="page-header">
  <div class="page-header-left">
    <h2><?= clean(($patient['first_name'] ?? '').' '.($patient['last_name'] ?? '')) ?></h2>
    <p>Patient profile and history</p>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <a href="<?= url('patients') ?>" class="btn btn-outline">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Back
    </a>
    <a href="<?= url('patients/edit?id='.$patient['id']) ?>" class="btn btn-primary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Edit
    </a>
  </div>
</div>

<div class="show-layout">

  <!-- ==================== LEFT COLUMN ==================== -->
  <div class="show-left">

    <!-- Profile card -->
    <div class="card">
      <div class="card-body profile-hero">
        <div class="profile-avatar" style="background:<?= $genderColor ?>20;color:<?= $genderColor ?>">
          <?= initials(($patient['first_name'] ?? 'U').' '.($patient['last_name'] ?? '')) ?>
        </div>
        <h3 class="profile-name"><?= clean(($patient['first_name'] ?? '').' '.($patient['last_name'] ?? '')) ?></h3>
        <?php if($age): ?>
        <p class="profile-sub"><?= $age ?> years old &middot; <?= ucfirst($patient['gender'] ?? 'other') ?></p>
        <?php endif; ?>
        <?php if(!empty($patient['insurance_name'])): ?>
        <span class="badge badge-info" style="margin-top:10px"><?= clean($patient['insurance_name']) ?></span>
        <?php else: ?>
        <span class="badge badge-secondary" style="margin-top:10px">No Insurance</span>
        <?php endif; ?>
      </div>

      <div style="height:1px;background:var(--border)"></div>

      <div class="card-body">
        <?php
        $fields = [
          ['icon'=>'phone',       'label'=>'Phone',       'value'=> $patient['phone']     ?? null],
          ['icon'=>'mail',        'label'=>'Email',       'value'=> $patient['email']     ?? null],
          ['icon'=>'map-pin',     'label'=>'Address',     'value'=> $patient['address']   ?? null],
          ['icon'=>'credit-card', 'label'=>'ID Number',   'value'=> $patient['id_number'] ?? null],
          ['icon'=>'calendar',    'label'=>'Birth Date',  'value'=> !empty($patient['birth_date']) ? formatDate($patient['birth_date']) : null],
          ['icon'=>'clock',       'label'=>'Registered',  'value'=> !empty($patient['created_at'])  ? formatDate($patient['created_at'])  : null],
        ];
        $icons = [
          'phone'       => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.6 1.23h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.82a16 16 0 0 0 6.27 6.27l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>',
          'mail'        => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
          'map-pin'     => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
          'credit-card' => '<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
          'calendar'    => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
          'clock'       => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        ];
        ?>
        <?php foreach($fields as $f): ?>
        <div class="profile-field">
          <div class="profile-field-icon">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--text-secondary)" stroke-width="2">
              <?= $icons[$f['icon']] ?? '' ?>
            </svg>
          </div>
          <div class="profile-field-content">
            <div class="profile-field-label"><?= $f['label'] ?></div>
            <div class="profile-field-value"><?= $f['value'] ? clean($f['value']) : '<span style="color:var(--text-muted)">—</span>' ?></div>
          </div>
        </div>
        <?php endforeach; ?>

        <?php if(!empty($patient['notes'])): ?>
        <div class="profile-notes">
          <div class="profile-field-label" style="margin-bottom:4px">Notes</div>
          <p style="font-size:0.85rem;color:var(--text-secondary);margin:0"><?= clean($patient['notes']) ?></p>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Medical summary -->
    <?php if($anamnesis): ?>
    <div class="card">
      <div class="card-header">
        <span class="card-title">Medical Summary</span>
        <a href="<?= url('anamnesis/show?id='.$anamnesis['id']) ?>" style="font-size:0.78rem;color:var(--primary)">View full</a>
      </div>
      <div class="card-body">
        <?php if(!empty($anamnesis['chief_complaint'])): ?>
        <div style="margin-bottom:14px">
          <div class="profile-field-label" style="margin-bottom:4px">Chief Complaint</div>
          <p style="font-size:0.875rem;margin:0"><?= clean($anamnesis['chief_complaint']) ?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($anamnesis['allergies_list'])): ?>
        <div style="margin-bottom:12px">
          <div class="profile-field-label" style="color:var(--danger);margin-bottom:6px">⚠ Allergies</div>
          <?php foreach(explode(', ', $anamnesis['allergies_list']) as $al): ?>
            <?php if(trim($al)): ?>
            <span class="badge badge-danger" style="margin-right:4px;margin-bottom:4px"><?= clean(trim($al)) ?></span>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if(!empty($anamnesis['conditions_list'])): ?>
        <div>
          <div class="profile-field-label" style="margin-bottom:6px">Conditions</div>
          <?php foreach(explode(', ', $anamnesis['conditions_list']) as $c): ?>
            <?php if(trim($c)): ?>
            <span class="badge badge-warning" style="margin-right:4px;margin-bottom:4px"><?= clean(trim($c)) ?></span>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php else: ?>
    <div class="card">
      <div class="card-header"><span class="card-title">Medical Summary</span></div>
      <div class="card-body">
        <div class="empty-state" style="padding:24px">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <h3>No anamnesis yet</h3>
          <p>No medical history recorded</p>
          <a href="<?= url('anamnesis/create?patient_id='.$patient['id']) ?>" class="btn btn-outline" style="margin-top:10px;font-size:0.8rem">Add Anamnesis</a>
        </div>
      </div>
    </div>
    <?php endif; ?>

  </div>

  <!-- ==================== RIGHT COLUMN ==================== -->
  <div class="show-right">
    <div class="card" style="height:100%">
      <div class="card-header">
        <span class="card-title">Appointment History</span>
        <span class="badge badge-secondary"><?= count($appointments) ?> records</span>
      </div>

      <?php if(count($appointments) > 0): ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Procedure</th>
              <th>Dentist</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($appointments as $a): ?>
            <tr>
              <td style="white-space:nowrap">
                <div style="font-size:0.85rem;font-weight:500"><?= formatDate($a['date'] ?? '') ?></div>
                <div style="font-size:0.72rem;color:var(--text-muted);font-family:var(--font-mono)">
                  <?= !empty($a['start_time']) ? substr($a['start_time'],0,5) : '' ?>
                </div>
              </td>
              <td>
                <span style="display:inline-flex;align-items:center;gap:5px;font-size:0.82rem">
                  <span style="width:8px;height:8px;border-radius:50%;background:<?= clean($a['procedure_color'] ?? '#888') ?>;flex-shrink:0;display:inline-block"></span>
                  <?= clean($a['procedure_name'] ?? '—') ?>
                </span>
              </td>
              <td style="font-size:0.82rem;color:var(--text-secondary)"><?= clean($a['dentist_name'] ?? '—') ?></td>
              <td><?= statusBadge($a['status'] ?? 'scheduled') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php else: ?>
      <div class="card-body">
        <div class="empty-state" style="padding:40px 24px">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <h3>No appointments yet</h3>
          <p>This patient has no appointment history</p>
          <a href="<?= url('schedule') ?>" class="btn btn-outline" style="margin-top:10px;font-size:0.8rem">Book Appointment</a>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<style>
/* Show page layout */
.show-layout {
  display: grid;
  grid-template-columns: 340px 1fr;
  gap: 20px;
  align-items: start;
}
.show-left  { display: flex; flex-direction: column; gap: 20px; }
.show-right { min-width: 0; }

/* Profile hero */
.profile-hero {
  text-align: center;
  padding: 32px 24px !important;
}
.profile-avatar {
  width: 72px; height: 72px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem; font-weight: 600;
  margin: 0 auto 14px;
}
.profile-name { font-size: 1.1rem; font-weight: 600; }
.profile-sub  { color: var(--text-secondary); font-size: 0.85rem; margin-top: 4px; }

/* Profile fields */
.profile-field {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 0;
  border-bottom: 1px solid var(--border-light);
}
.profile-field:last-of-type { border-bottom: none; }
.profile-field-icon {
  width: 32px; height: 32px;
  background: var(--gray-100);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.profile-field-label {
  font-size: 0.7rem;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: .04em;
}
.profile-field-value {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--text);
  word-break: break-word;
}
.profile-notes {
  margin-top: 12px;
  padding: 10px;
  background: var(--gray-50);
  border-radius: 8px;
}

/* Responsive */
@media (max-width: 900px) {
  .show-layout {
    grid-template-columns: 1fr;
  }
}
@media (max-width: 480px) {
  .profile-hero { padding: 24px 16px !important; }
  .profile-avatar { width: 56px; height: 56px; font-size: 1.1rem; }
}
</style>
