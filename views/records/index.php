<!-- Page header -->
<div class="page-header">
  <div class="page-header-left">
    <h2>Clinical Records</h2>
    <p>Treatment history for all patients</p>
  </div>
  <a href="<?= url('records/create') ?>" class="btn btn-primary">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Record
  </a>
</div>

<!-- Stats -->
<div class="kpi-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px">
  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon blue">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      </div>
      <div>
        <div class="kpi-label">Total Records</div>
        <div class="kpi-value"><?= $stats['total'] ?></div>
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
  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon orange">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      </div>
      <div>
        <div class="kpi-label">Top Procedure</div>
        <div class="kpi-value" style="font-size:0.95rem"><?= clean($stats['top_proc']) ?></div>
      </div>
    </div>
  </div>
</div>

<!-- Filters + table -->
<div class="card">
  <div class="card-header" style="flex-wrap:wrap;gap:10px">
    <div class="search-bar" style="flex:1;min-width:200px;max-width:300px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" id="searchInput" class="search-input" placeholder="Search patient or procedure..." value="<?= clean($search) ?>">
    </div>
    <select id="patientFilter" class="form-select" style="width:auto;max-width:220px" onchange="filterByPatient(this.value)">
      <option value="0">All patients</option>
      <?php foreach($patients as $p): ?>
      <option value="<?= $p['id'] ?>" <?= $patientId == $p['id'] ? 'selected' : '' ?>>
        <?= clean($p['full_name']) ?>
      </option>
      <?php endforeach; ?>
    </select>
    <span id="resultCount" style="font-size:0.8rem;color:var(--text-muted);margin-left:auto"><?= count($records) ?> records</span>
  </div>

  <?php if(count($records) > 0): ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Patient</th>
          <th>Procedure</th>
          <th>Dentist</th>
          <th>Teeth</th>
          <th>Duration</th>
          <th>Date</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody id="recordsTbody">
        <?php foreach($records as $r): ?>
        <tr class="rec-row" data-search="<?= clean(strtolower(($r['patient_name'] ?? '').' '.($r['procedure_name'] ?? ''))) ?>">
          <td>
            <div style="display:flex;align-items:center;gap:9px">
              <div class="patient-avatar" style="background:var(--primary-light);color:var(--primary-dark)">
                <?= initials($r['patient_name'] ?? 'U') ?>
              </div>
              <div>
                <div style="font-weight:500;font-size:0.875rem"><?= clean($r['patient_name'] ?? '—') ?></div>
                <div style="font-size:0.72rem;color:var(--text-muted)"><?= clean($r['patient_phone'] ?? '') ?></div>
              </div>
            </div>
          </td>
          <td>
            <div style="display:flex;align-items:center;gap:6px">
              <span style="width:9px;height:9px;border-radius:50%;background:<?= clean($r['procedure_color'] ?? '#888') ?>;flex-shrink:0;display:inline-block"></span>
              <div>
                <div style="font-size:0.85rem;font-weight:500"><?= clean($r['procedure_name'] ?? '—') ?></div>
                <div style="font-size:0.7rem;color:var(--text-muted)"><?= clean($r['procedure_category'] ?? '') ?></div>
              </div>
            </div>
          </td>
          <td>
            <div style="display:flex;align-items:center;gap:6px">
              <span style="width:9px;height:9px;border-radius:50%;background:<?= clean($r['dentist_color'] ?? '#888') ?>;flex-shrink:0;display:inline-block"></span>
              <div>
                <div style="font-size:0.85rem"><?= clean($r['dentist_name'] ?? '—') ?></div>
                <div style="font-size:0.7rem;color:var(--text-muted)"><?= clean($r['dentist_specialty'] ?? '') ?></div>
              </div>
            </div>
          </td>
          <td>
            <?php if(!empty($r['teeth'])): ?>
            <div style="display:flex;flex-wrap:wrap;gap:3px">
              <?php foreach(explode(',', $r['teeth']) as $t): ?>
              <span class="tooth-badge"><?= clean(trim($t)) ?></span>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
            <span style="color:var(--text-muted);font-size:0.8rem">—</span>
            <?php endif; ?>
          </td>
          <td style="font-size:0.85rem;color:var(--text-secondary)">
            <?= $r['duration'] ? $r['duration'].' min' : '—' ?>
          </td>
          <td style="font-size:0.82rem;color:var(--text-secondary);white-space:nowrap">
            <?= formatDate($r['created_at'] ?? '') ?>
          </td>
          <td>
            <div style="display:flex;justify-content:flex-end;gap:6px">
              <a href="<?= url('records/show?id='.$r['id']) ?>" class="action-btn" title="View">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </a>
              <a href="<?= url('records/edit?id='.$r['id']) ?>" class="action-btn" title="Edit">
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
      <h3>No clinical records yet</h3>
      <p>Create the first treatment record</p>
      <a href="<?= url('records/create') ?>" class="btn btn-primary" style="margin-top:14px">New Record</a>
    </div>
  </div>
  <?php endif; ?>
</div>
