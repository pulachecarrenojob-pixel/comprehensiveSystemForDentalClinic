<?php
$genderIcon = ['male'=>'♂','female'=>'♀','other'=>'⚧'];
?>

<!-- Page header -->
<div class="page-header">
  <div class="page-header-left">
    <h2>Patients</h2>
    <p>Manage all registered patients</p>
  </div>
  <a href="<?= url('patients/create') ?>" class="btn btn-primary">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Patient
  </a>
</div>

<!-- Stats row -->
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px">
  <div class="kpi-card" style="padding:16px 20px">
    <div class="kpi-left">
      <div class="kpi-icon blue">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      </div>
      <div>
        <div class="kpi-label">Total Patients</div>
        <div class="kpi-value" style="font-size:1.4rem"><?= $stats['total'] ?></div>
      </div>
    </div>
  </div>
  <div class="kpi-card" style="padding:16px 20px">
    <div class="kpi-left">
      <div class="kpi-icon green">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
      </div>
      <div>
        <div class="kpi-label">New This Month</div>
        <div class="kpi-value" style="font-size:1.4rem"><?= $stats['new_month'] ?></div>
      </div>
    </div>
  </div>
  <div class="kpi-card" style="padding:16px 20px">
    <div class="kpi-left">
      <div class="kpi-icon orange">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </div>
      <div>
        <div class="kpi-label">With Insurance</div>
        <div class="kpi-value" style="font-size:1.4rem"><?= $stats['with_insurance'] ?></div>
      </div>
    </div>
  </div>
</div>

<!-- Search & filter -->
<div class="card">
  <div class="card-header" style="gap:12px;flex-wrap:wrap">
    <div class="search-bar" style="max-width:360px;flex:1">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input
        type="text"
        id="searchInput"
        class="search-input"
        placeholder="Search by name, phone, email or ID..."
        value="<?= clean($search) ?>"
        autocomplete="off"
      >
    </div>
    <div style="display:flex;gap:8px;align-items:center">
      <span id="resultCount" style="font-size:0.8rem;color:var(--text-muted)"><?= count($patients) ?> patients</span>
    </div>
  </div>

  <!-- Table -->
  <div class="table-wrap" id="patientsTable">
    <?php if(count($patients) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Patient</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Birth date</th>
          <th>Insurance</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody id="patientsTbody">
        <?php foreach($patients as $p): ?>
        <tr class="patient-row" data-name="<?= clean($p['first_name'].' '.$p['last_name']) ?>">
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <div class="patient-avatar" style="background:<?= patientAvatarColor($p['gender']) ?>20;color:<?= patientAvatarColor($p['gender']) ?>">
                <?= initials($p['first_name'].' '.$p['last_name']) ?>
              </div>
              <div>
                <div style="font-weight:500;font-size:0.9rem"><?= clean($p['first_name'].' '.$p['last_name']) ?></div>
                <div style="font-size:0.75rem;color:var(--text-muted)">
                  <?= $p['id_number'] ? 'ID: '.clean($p['id_number']) : 'No ID' ?>
                </div>
              </div>
            </div>
          </td>
          <td style="font-size:0.875rem"><?= clean($p['phone']) ?></td>
          <td style="font-size:0.875rem;color:var(--text-secondary)"><?= clean($p['email'] ?? '—') ?></td>
          <td style="font-size:0.875rem">
            <?= $p['birth_date'] ? formatDate($p['birth_date']) : '—' ?>
            <?php if($p['birth_date']): ?>
            <span style="font-size:0.72rem;color:var(--text-muted);margin-left:4px">
              (<?= (int)date_diff(date_create($p['birth_date']), date_create('today'))->y ?>y)
            </span>
            <?php endif; ?>
          </td>
          <td>
            <?php if($p['insurance_name']): ?>
            <span class="badge badge-info"><?= clean($p['insurance_name']) ?></span>
            <?php else: ?>
            <span style="font-size:0.8rem;color:var(--text-muted)">—</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="display:flex;justify-content:flex-end;gap:6px">
              <a href="<?= url('patients/show?id='.$p['id']) ?>" class="action-btn" title="View">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </a>
              <a href="<?= url('patients/edit?id='.$p['id']) ?>" class="action-btn" title="Edit">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <button class="action-btn danger" onclick="confirmDelete(<?= $p['id'] ?>, '<?= clean($p['first_name'].' '.$p['last_name']) ?>')" title="Delete">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
              </button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      <h3>No patients found</h3>
      <p>Add your first patient to get started</p>
      <a href="<?= url('patients/create') ?>" class="btn btn-primary" style="margin-top:14px">Add Patient</a>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- Delete modal -->
<div class="modal-backdrop" id="deleteModal">
  <div class="modal" style="max-width:400px">
    <div class="modal-header">
      <span class="modal-title">Remove Patient</span>
      <button class="modal-close" onclick="closeModal('deleteModal')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <p style="color:var(--text-secondary);font-size:0.9rem">
        Are you sure you want to remove <strong id="deletePatientName"></strong>?
        This action cannot be undone.
      </p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('deleteModal')">Cancel</button>
      <form id="deleteForm" method="POST" action="<?= url('patients/delete') ?>" style="display:inline">
        <?= csrfField() ?>
        <input type="hidden" name="id" id="deleteId">
        <button type="submit" class="btn btn-danger">Remove Patient</button>
      </form>
    </div>
  </div>
</div>

<?php
function patientAvatarColor(string $gender): string {
    return match($gender) { 'female' => '#D4537E', 'male' => '#378ADD', default => '#888780' };
}
?>
