<?php
$severityBadge = fn(string $s) => match($s) {
    'severe'   => '<span class="badge badge-danger">Severe</span>',
    'moderate' => '<span class="badge badge-warning">Moderate</span>',
    default    => '<span class="badge badge-secondary">Mild</span>',
};
?>

<div class="page-header">
  <div class="page-header-left">
    <h2><?= clean($anamnesis['patient_name'] ?? '') ?></h2>
    <p>Anamnesis — <?= formatDate($anamnesis['created_at'] ?? '') ?></p>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <a href="<?= url('anamnesis') ?>" class="btn btn-outline">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Back
    </a>
    <a href="<?= url('anamnesis/edit?id='.$anamnesis['id']) ?>" class="btn btn-primary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Edit
    </a>
  </div>
</div>

<div class="ana-show-layout">

  <!-- ===== LEFT ===== -->
  <div class="ana-show-left">

    <!-- Patient info pill -->
    <div class="card">
      <div class="card-body" style="display:flex;align-items:center;gap:14px;padding:20px">
        <div class="patient-avatar" style="width:52px;height:52px;font-size:1rem;background:var(--primary-light);color:var(--primary-dark);flex-shrink:0">
          <?= initials($anamnesis['patient_name'] ?? 'U') ?>
        </div>
        <div>
          <div style="font-size:1rem;font-weight:600"><?= clean($anamnesis['patient_name'] ?? '') ?></div>
          <div style="font-size:0.8rem;color:var(--text-secondary);margin-top:2px"><?= clean($anamnesis['patient_phone'] ?? '') ?></div>
        </div>
        <div style="margin-left:auto;text-align:right">
          <div style="font-size:0.72rem;color:var(--text-muted)">Recorded by</div>
          <div style="font-size:0.82rem;font-weight:500"><?= clean($anamnesis['created_by_name'] ?? '') ?></div>
        </div>
      </div>
    </div>

    <!-- Vital info -->
    <div class="card">
      <div class="card-header"><span class="card-title">Vital Information</span></div>
      <div class="card-body">
        <div class="ana-vitals">
          <?php
          $vitals = [
            ['label'=>'Blood Type', 'value'=> $anamnesis['blood_type'] ?: 'Unknown', 'highlight'=> (bool)$anamnesis['blood_type']],
            ['label'=>'Smoker',     'value'=> $anamnesis['smoker']    ? 'Yes' : 'No', 'highlight'=> (bool)$anamnesis['smoker']],
            ['label'=>'Pregnant',   'value'=> $anamnesis['pregnant']  ? 'Yes' : 'No', 'highlight'=> (bool)$anamnesis['pregnant']],
          ];
          foreach($vitals as $v): ?>
          <div class="vital-item <?= $v['highlight'] ? 'vital-highlight' : '' ?>">
            <div class="vital-label"><?= $v['label'] ?></div>
            <div class="vital-value"><?= $v['value'] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Allergies -->
    <div class="card <?= count($allergies) ? 'has-alert' : '' ?>">
      <div class="card-header">
        <span class="card-title">
          <?php if(count($allergies)): ?>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:5px"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          <?php endif; ?>
          Allergies
        </span>
        <span class="badge <?= count($allergies) ? 'badge-danger' : 'badge-secondary' ?>"><?= count($allergies) ?></span>
      </div>
      <div class="card-body">
        <?php if(count($allergies)): ?>
        <div style="display:flex;flex-direction:column;gap:8px">
          <?php foreach($allergies as $al): ?>
          <div class="allergy-item">
            <div style="display:flex;align-items:center;gap:8px">
              <div class="allergy-dot severity-<?= $al['severity'] ?>"></div>
              <span style="font-size:0.875rem;font-weight:500"><?= clean($al['name']) ?></span>
            </div>
            <?= $severityBadge($al['severity']) ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="font-size:0.85rem;color:var(--text-muted);text-align:center;padding:8px 0">No allergies recorded</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Conditions -->
    <div class="card">
      <div class="card-header">
        <span class="card-title">Medical Conditions</span>
        <span class="badge badge-secondary"><?= count($conditions) ?></span>
      </div>
      <div class="card-body">
        <?php if(count($conditions)): ?>
        <div style="display:flex;flex-wrap:wrap;gap:8px">
          <?php foreach($conditions as $c): ?>
          <span class="badge badge-warning" style="font-size:0.8rem;padding:5px 12px"><?= clean($c['name'] ?? '') ?></span>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="font-size:0.85rem;color:var(--text-muted);text-align:center;padding:8px 0">No conditions recorded</p>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <!-- ===== RIGHT ===== -->
  <div class="ana-show-right">

    <!-- Chief complaint -->
    <div class="card">
      <div class="card-header"><span class="card-title">Chief Complaint</span></div>
      <div class="card-body">
        <p style="font-size:0.9rem;line-height:1.7;color:var(--text)"><?= clean($anamnesis['chief_complaint'] ?? '—') ?></p>
      </div>
    </div>

    <!-- Medical history -->
    <div class="card">
      <div class="card-header"><span class="card-title">Medical History</span></div>
      <div class="card-body">
        <?php if(!empty($anamnesis['medical_history'])): ?>
        <p style="font-size:0.875rem;line-height:1.7;color:var(--text-secondary)"><?= nl2br(clean($anamnesis['medical_history'])) ?></p>
        <?php else: ?>
        <p style="font-size:0.85rem;color:var(--text-muted)">No medical history recorded.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Current medications -->
    <div class="card">
      <div class="card-header"><span class="card-title">Current Medications</span></div>
      <div class="card-body">
        <?php if(!empty($anamnesis['current_meds'])): ?>
        <p style="font-size:0.875rem;line-height:1.7;color:var(--text-secondary)"><?= nl2br(clean($anamnesis['current_meds'])) ?></p>
        <?php else: ?>
        <p style="font-size:0.85rem;color:var(--text-muted)">No medications recorded.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Notes -->
    <?php if(!empty($anamnesis['notes'])): ?>
    <div class="card">
      <div class="card-header"><span class="card-title">Additional Notes</span></div>
      <div class="card-body">
        <p style="font-size:0.875rem;line-height:1.7;color:var(--text-secondary)"><?= nl2br(clean($anamnesis['notes'])) ?></p>
      </div>
    </div>
    <?php endif; ?>

  </div>

</div>
