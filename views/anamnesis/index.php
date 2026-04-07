<!-- Page header -->
<div class="page-header">
  <div class="page-header-left">
    <h2>Anamnesis</h2>
    <p>Medical history records for all patients</p>
  </div>
  <a href="<?= url('anamnesis/create') ?>" class="btn btn-primary">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Anamnesis
  </a>
</div>

<!-- Stats -->
<div class="kpi-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px">
  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon blue">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      </div>
      <div>
        <div class="kpi-label">Total Records</div>
        <div class="kpi-value"><?= $stats['total'] ?></div>
      </div>
    </div>
  </div>
  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon red">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      </div>
      <div>
        <div class="kpi-label">With Allergies</div>
        <div class="kpi-value"><?= $stats['with_allergies'] ?></div>
      </div>
    </div>
  </div>
  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon green">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
      <div>
        <div class="kpi-label">This Month</div>
        <div class="kpi-value"><?= $stats['this_month'] ?></div>
      </div>
    </div>
  </div>
</div>

<!-- Search + list -->
<div class="card">
  <div class="card-header">
    <div class="search-bar" style="flex:1;max-width:340px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="searchInput" class="search-input" placeholder="Search by patient name...">
    </div>
    <span id="resultCount" style="font-size:0.8rem;color:var(--text-muted)"><?= count($list) ?> records</span>
  </div>

  <?php if(count($list) > 0): ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Patient</th>
          <th>Chief Complaint</th>
          <th>Allergies</th>
          <th>Conditions</th>
          <th>Date</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody id="anamnesisBody">
        <?php foreach($list as $row):
          $allergies  = AnamnesisModel::parsePiped($row['allergies_raw'] ?? '');
          $conditions = AnamnesisModel::parsePiped($row['conditions_raw'] ?? '');
        ?>
        <tr class="ana-row" data-name="<?= clean($row['patient_name'] ?? '') ?>">
          <td>
            <div style="display:flex;align-items:center;gap:9px">
              <div class="patient-avatar" style="background:var(--primary-light);color:var(--primary-dark)">
                <?= initials($row['patient_name'] ?? 'U') ?>
              </div>
              <div>
                <div style="font-weight:500;font-size:0.875rem"><?= clean($row['patient_name'] ?? '—') ?></div>
                <div style="font-size:0.72rem;color:var(--text-muted)"><?= clean($row['patient_phone'] ?? '') ?></div>
              </div>
            </div>
          </td>
          <td style="max-width:200px">
            <p style="font-size:0.82rem;color:var(--text-secondary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px">
              <?= clean($row['chief_complaint'] ?? '—') ?>
            </p>
          </td>
          <td>
            <?php if($allergies): ?>
              <?php foreach(array_slice($allergies, 0, 2) as $al): ?>
              <span class="badge badge-danger" style="margin-right:3px;margin-bottom:2px"><?= clean($al) ?></span>
              <?php endforeach; ?>
              <?php if(count($allergies) > 2): ?>
              <span class="badge badge-secondary">+<?= count($allergies)-2 ?></span>
              <?php endif; ?>
            <?php else: ?>
              <span style="font-size:0.8rem;color:var(--text-muted)">None</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if($conditions): ?>
              <?php foreach(array_slice($conditions, 0, 2) as $c): ?>
              <span class="badge badge-warning" style="margin-right:3px;margin-bottom:2px"><?= clean($c) ?></span>
              <?php endforeach; ?>
              <?php if(count($conditions) > 2): ?>
              <span class="badge badge-secondary">+<?= count($conditions)-2 ?></span>
              <?php endif; ?>
            <?php else: ?>
              <span style="font-size:0.8rem;color:var(--text-muted)">None</span>
            <?php endif; ?>
          </td>
          <td style="font-size:0.82rem;white-space:nowrap;color:var(--text-secondary)">
            <?= formatDate($row['created_at'] ?? '') ?>
          </td>
          <td>
            <div style="display:flex;justify-content:flex-end;gap:6px">
              <a href="<?= url('anamnesis/show?id='.$row['id']) ?>" class="action-btn" title="View">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </a>
              <a href="<?= url('anamnesis/edit?id='.$row['id']) ?>" class="action-btn" title="Edit">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="card-body">
    <div class="empty-state">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      <h3>No anamnesis records yet</h3>
      <p>Create the first medical history record</p>
      <a href="<?= url('anamnesis/create') ?>" class="btn btn-primary" style="margin-top:14px">New Anamnesis</a>
    </div>
  </div>
  <?php endif; ?>
</div>
