<div class="page-header">
  <div class="page-header-left">
    <h2><?= clean($record['patient_name'] ?? '') ?></h2>
    <p>Clinical Record — <?= formatDate($record['created_at'] ?? '') ?></p>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
    <a href="<?= url('records') ?>" class="btn btn-outline">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
      Back
    </a>
    <a href="<?= url('records/edit?id='.$record['id']) ?>" class="btn btn-primary">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Edit
    </a>
  </div>
</div>

<div class="rec-show-layout">

  <!-- ===== LEFT ===== -->
  <div class="rec-show-left">

    <!-- Patient card -->
    <div class="card">
      <div class="card-body" style="display:flex;align-items:center;gap:12px;padding:18px 20px">
        <div class="patient-avatar" style="width:50px;height:50px;font-size:1rem;background:var(--primary-light);color:var(--primary-dark);flex-shrink:0">
          <?= initials($record['patient_name'] ?? 'U') ?>
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-weight:600;font-size:1rem"><?= clean($record['patient_name'] ?? '') ?></div>
          <div style="font-size:0.78rem;color:var(--text-muted);margin-top:2px"><?= clean($record['patient_phone'] ?? '') ?></div>
        </div>
        <a href="<?= url('patients/show?id='.$record['patient_id']) ?>" class="btn btn-outline" style="padding:5px 12px;font-size:0.78rem">Profile</a>
      </div>
    </div>

    <!-- Treatment info -->
    <div class="card">
      <div class="card-header"><span class="card-title">Treatment Details</span></div>
      <div class="card-body">

        <!-- Procedure -->
        <div class="rec-detail-row">
          <div class="rec-detail-icon" style="background:<?= clean($record['procedure_color'] ?? '#888') ?>20;color:<?= clean($record['procedure_color'] ?? '#888') ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          </div>
          <div>
            <div class="rec-detail-label">Procedure</div>
            <div class="rec-detail-value"><?= clean($record['procedure_name'] ?? '—') ?></div>
            <div style="font-size:0.72rem;color:var(--text-muted)"><?= clean($record['procedure_category'] ?? '') ?></div>
          </div>
        </div>

        <!-- Dentist -->
        <div class="rec-detail-row">
          <div class="rec-detail-icon" style="background:<?= clean($record['dentist_color'] ?? '#888') ?>20;color:<?= clean($record['dentist_color'] ?? '#888') ?>">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </div>
          <div>
            <div class="rec-detail-label">Dentist</div>
            <div class="rec-detail-value"><?= clean($record['dentist_name'] ?? '—') ?></div>
            <div style="font-size:0.72rem;color:var(--text-muted)"><?= clean($record['dentist_specialty'] ?? '') ?></div>
          </div>
        </div>

        <!-- Duration -->
        <div class="rec-detail-row">
          <div class="rec-detail-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          </div>
          <div>
            <div class="rec-detail-label">Duration</div>
            <div class="rec-detail-value"><?= $record['duration'] ? $record['duration'].' minutes' : 'Not recorded' ?></div>
          </div>
        </div>

        <!-- Appointment date -->
        <?php if(!empty($record['appointment_date'])): ?>
        <div class="rec-detail-row" style="border-bottom:none">
          <div class="rec-detail-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </div>
          <div>
            <div class="rec-detail-label">Appointment</div>
            <div class="rec-detail-value">
              <?= formatDate($record['appointment_date']) ?>
              <?= !empty($record['start_time']) ? '· '.substr($record['start_time'],0,5) : '' ?>
            </div>
          </div>
        </div>
        <?php endif; ?>

      </div>
    </div>

    <!-- Teeth -->
    <div class="card">
      <div class="card-header"><span class="card-title">Teeth Involved</span></div>
      <div class="card-body">
        <?php if(!empty($record['teeth'])): ?>
        <div style="display:flex;flex-wrap:wrap;gap:6px">
          <?php foreach(explode(',', $record['teeth']) as $t): ?>
          <span class="tooth-badge tooth-badge-lg"><?= clean(trim($t)) ?></span>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="font-size:0.85rem;color:var(--text-muted)">No specific teeth recorded</p>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <!-- ===== RIGHT ===== -->
  <div class="rec-show-right">

    <!-- Description -->
    <div class="card">
      <div class="card-header"><span class="card-title">Procedure Description</span></div>
      <div class="card-body">
        <p style="font-size:0.9rem;line-height:1.8;color:var(--text);white-space:pre-line"><?= clean($record['description'] ?? '—') ?></p>
      </div>
    </div>

    <!-- Observations -->
    <div class="card">
      <div class="card-header"><span class="card-title">Observations & Follow-up</span></div>
      <div class="card-body">
        <?php if(!empty($record['observations'])): ?>
        <p style="font-size:0.875rem;line-height:1.8;color:var(--text-secondary);white-space:pre-line"><?= clean($record['observations']) ?></p>
        <?php else: ?>
        <p style="font-size:0.85rem;color:var(--text-muted)">No observations recorded.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Standard price info -->
    <?php if(!empty($record['procedure_price'])): ?>
    <div class="card">
      <div class="card-header"><span class="card-title">Billing Reference</span></div>
      <div class="card-body" style="display:flex;align-items:center;justify-content:space-between">
        <div>
          <div style="font-size:0.78rem;color:var(--text-muted)">Standard procedure price</div>
          <div style="font-size:1.4rem;font-weight:600;color:var(--primary)"><?= formatMoney($record['procedure_price']) ?></div>
        </div>
        <a href="<?= url('finance') ?>" class="btn btn-outline" style="font-size:0.8rem">
          Register Payment
        </a>
      </div>
    </div>
    <?php endif; ?>

  </div>

</div>
