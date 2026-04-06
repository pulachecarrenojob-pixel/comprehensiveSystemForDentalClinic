<?php
$age = $patient['birth_date']
    ? (int)date_diff(date_create($patient['birth_date']), date_create('today'))->y
    : null;
$genderColor = match($patient['gender']) { 'female' => '#D4537E', 'male' => '#378ADD', default => '#888780' };
?>

<div class="page-header">
  <div class="page-header-left">
    <h2><?= clean($patient['first_name'].' '.$patient['last_name']) ?></h2>
    <p>Patient profile and history</p>
  </div>
  <div style="display:flex;gap:8px">
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

<div class="grid-2" style="gap:20px;align-items:start">

  <!-- Left column -->
  <div style="display:flex;flex-direction:column;gap:20px">

    <!-- Profile card -->
    <div class="card">
      <div class="card-body" style="text-align:center;padding:32px 24px">
        <div style="width:72px;height:72px;border-radius:50%;background:<?= $genderColor ?>20;color:<?= $genderColor ?>;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:600;margin:0 auto 14px">
          <?= initials($patient['first_name'].' '.$patient['last_name']) ?>
        </div>
        <h3 style="font-size:1.1rem;font-weight:600"><?= clean($patient['first_name'].' '.$patient['last_name']) ?></h3>
        <?php if($age): ?>
        <p style="color:var(--text-secondary);font-size:0.85rem;margin-top:4px"><?= $age ?> years old · <?= ucfirst($patient['gender']) ?></p>
        <?php endif; ?>
        <?php if($patient['insurance_name']): ?>
        <span class="badge badge-info" style="margin-top:10px"><?= clean($patient['insurance_name']) ?></span>
        <?php endif; ?>
      </div>
      <div class="divider" style="margin:0"></div>
      <div class="card-body">
        <?php $fields = [
          ['icon'=>'phone','label'=>'Phone','value'=>$patient['phone']],
          ['icon'=>'mail','label'=>'Email','value'=>$patient['email']],
          ['icon'=>'map-pin','label'=>'Address','value'=>$patient['address']],
          ['icon'=>'credit-card','label'=>'ID Number','value'=>$patient['id_number']],
          ['icon'=>'calendar','label'=>'Birth Date','value'=>$patient['birth_date'] ? formatDate($patient['birth_date']) : null],
          ['icon'=>'clock','label'=>'Registered','value'=>formatDate($patient['created_at'])],
        ]; ?>
        <?php foreach($fields as $f): if(!$f['value']) continue; ?>
        <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border-light)">
          <div style="width:32px;height:32px;background:var(--gray-100);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--text-secondary)" stroke-width="2">
              <?php if($f['icon']==='phone'): ?><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.6 1.23h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.82a16 16 0 0 0 6.27 6.27l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/><?php endif; ?>
              <?php if($f['icon']==='mail'): ?><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/><?php endif; ?>
              <?php if($f['icon']==='map-pin'): ?><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/><?php endif; ?>
              <?php if($f['icon']==='credit-card'): ?><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/><?php endif; ?>
              <?php if($f['icon']==='calendar'): ?><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><?php endif; ?>
              <?php if($f['icon']==='clock'): ?><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/><?php endif; ?>
            </svg>
          </div>
          <div>
            <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em"><?= $f['label'] ?></div>
            <div style="font-size:0.875rem;font-weight:500;color:var(--text)"><?= clean($f['value']) ?></div>
          </div>
        </div>
        <?php endforeach; ?>

        <?php if($patient['notes']): ?>
        <div style="margin-top:12px;padding:10px;background:var(--gray-50);border-radius:8px">
          <div style="font-size:0.72rem;color:var(--text-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em">Notes</div>
          <p style="font-size:0.85rem;color:var(--text-secondary)"><?= clean($patient['notes']) ?></p>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Anamnesis summary -->
    <?php if($anamnesis): ?>
    <div class="card">
      <div class="card-header">
        <span class="card-title">Medical Summary</span>
        <a href="<?= url('anamnesis/show?id='.$anamnesis['id']) ?>" style="font-size:0.78rem;color:var(--primary)">View full</a>
      </div>
      <div class="card-body">
        <div style="margin-bottom:12px">
          <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px">Chief Complaint</div>
          <p style="font-size:0.875rem"><?= clean($anamnesis['chief_complaint']) ?></p>
        </div>
        <?php if($anamnesis['allergies_list']): ?>
        <div style="margin-bottom:12px">
          <div style="font-size:0.72rem;color:var(--danger);text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">⚠ Allergies</div>
          <?php foreach(explode(', ', $anamnesis['allergies_list']) as $al): ?>
          <span class="badge badge-danger" style="margin-right:4px;margin-bottom:4px"><?= clean($al) ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if($anamnesis['conditions_list']): ?>
        <div>
          <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Conditions</div>
          <?php foreach(explode(', ', $anamnesis['conditions_list']) as $c): ?>
          <span class="badge badge-warning" style="margin-right:4px;margin-bottom:4px"><?= clean($c) ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>

  <!-- Right: appointment history -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Appointment History</span>
      <span style="font-size:0.78rem;color:var(--text-muted)"><?= count($appointments) ?> records</span>
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
            <td style="font-size:0.82rem;white-space:nowrap">
              <div style="font-weight:500"><?= formatDate($a['date']) ?></div>
              <div style="color:var(--text-muted);font-size:0.72rem;font-family:var(--font-mono)"><?= substr($a['start_time'],0,5) ?></div>
            </td>
            <td>
              <span style="display:inline-flex;align-items:center;gap:5px;font-size:0.82rem">
                <span style="width:8px;height:8px;border-radius:50%;background:<?= clean($a['procedure_color']) ?>;flex-shrink:0;display:inline-block"></span>
                <?= clean($a['procedure_name']) ?>
              </span>
            </td>
            <td style="font-size:0.82rem;color:var(--text-secondary)"><?= clean($a['dentist_name']) ?></td>
            <td><?= statusBadge($a['status']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="card-body">
      <div class="empty-state" style="padding:32px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <h3>No appointments yet</h3>
        <p>This patient has no appointment history</p>
      </div>
    </div>
    <?php endif; ?>
  </div>

</div>
