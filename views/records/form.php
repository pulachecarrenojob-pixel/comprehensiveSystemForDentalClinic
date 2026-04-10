<?php
$isEdit = !empty($record);
$action = $isEdit ? url('records/update') : url('records/store');

// Group procedures by category for optgroup
$byCategory = [];
foreach ($procedures as $p) {
    $byCategory[$p['category']][] = $p;
}
?>

<div class="page-header">
  <div class="page-header-left">
    <h2><?= $isEdit ? 'Edit Record' : 'New Clinical Record' ?></h2>
    <p><?= $isEdit ? 'Update treatment record for '.clean($record['patient_name'] ?? '') : 'Register a new treatment record' ?></p>
  </div>
  <a href="<?= $isEdit ? url('records/show?id='.$record['id']) : url('records') ?>" class="btn btn-outline">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Back
  </a>
</div>

<form method="POST" action="<?= $action ?>" id="recordForm">
  <?= csrfField() ?>
  <?php if($isEdit): ?>
  <input type="hidden" name="id" value="<?= $record['id'] ?>">
  <?php endif; ?>

  <div class="rec-form-layout">

    <!-- ===== LEFT ===== -->
    <div class="rec-form-main">

      <!-- Patient + appointment link -->
      <?php if(!$isEdit): ?>
      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:6px"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Patient
          </span>
        </div>
        <div class="card-body">
          <div class="form-row">
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label">Patient <span style="color:var(--danger)">*</span></label>
              <select name="patient_id" id="patientSelect" class="form-select" required onchange="loadAppointments(this.value)">
                <option value="">Select patient...</option>
                <?php foreach($patients as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $preselect == $p['id'] ? 'selected' : '' ?>>
                  <?= clean($p['full_name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label">Link to Appointment <span style="font-size:0.72rem;color:var(--text-muted)">(optional)</span></label>
              <select name="appointment_id" id="appointmentSelect" class="form-select">
                <option value="">Select appointment...</option>
                <?php foreach($appointments as $a): ?>
                <option value="<?= $a['id'] ?>">
                  <?= formatDate($a['date']) ?> — <?= clean($a['procedure_name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
      </div>
      <?php else: ?>
      <input type="hidden" name="patient_id" value="<?= $record['patient_id'] ?>">
      <div class="card">
        <div class="card-body" style="display:flex;align-items:center;gap:12px;padding:16px 20px">
          <div class="patient-avatar" style="background:var(--primary-light);color:var(--primary-dark);width:44px;height:44px">
            <?= initials($record['patient_name'] ?? 'U') ?>
          </div>
          <div>
            <div style="font-weight:600;font-size:0.95rem"><?= clean($record['patient_name'] ?? '') ?></div>
            <div style="font-size:0.78rem;color:var(--text-muted)">Editing existing record</div>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Description -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:6px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
            Procedure Description
          </span>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label class="form-label">Description <span style="color:var(--danger)">*</span></label>
            <textarea name="description" class="form-textarea" rows="5" required
              placeholder="Detailed description of the procedure performed, findings, techniques used..."><?= clean($record['description'] ?? '') ?></textarea>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Observations & Follow-up</label>
            <textarea name="observations" class="form-textarea" rows="3"
              placeholder="Post-treatment observations, patient reactions, follow-up instructions..."><?= clean($record['observations'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <!-- Tooth chart -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:6px"><path d="M12 2C9 2 7 4 7 7c0 2 1 3.5 2 5l1 9h4l1-9c1-1.5 2-3 2-5 0-3-2-5-5-5z"/></svg>
            Teeth Involved
          </span>
          <span style="font-size:0.75rem;color:var(--text-muted)">Click to select</span>
        </div>
        <div class="card-body">
          <!-- Tooth chart visual -->
          <div class="tooth-chart">
            <div class="tooth-row-label">Upper Right</div>
            <div class="tooth-row" id="upperRight">
              <?php foreach([18,17,16,15,14,13,12,11] as $t): ?>
              <div class="tooth" data-num="<?= $t ?>" onclick="toggleTooth(<?= $t ?>)"><?= $t ?></div>
              <?php endforeach; ?>
            </div>
            <div class="tooth-row-label">Upper Left</div>
            <div class="tooth-row" id="upperLeft">
              <?php foreach([21,22,23,24,25,26,27,28] as $t): ?>
              <div class="tooth" data-num="<?= $t ?>" onclick="toggleTooth(<?= $t ?>)"><?= $t ?></div>
              <?php endforeach; ?>
            </div>
            <div style="height:8px;border-top:2px dashed var(--border);margin:8px 0"></div>
            <div class="tooth-row-label">Lower Right</div>
            <div class="tooth-row" id="lowerRight">
              <?php foreach([48,47,46,45,44,43,42,41] as $t): ?>
              <div class="tooth" data-num="<?= $t ?>" onclick="toggleTooth(<?= $t ?>)"><?= $t ?></div>
              <?php endforeach; ?>
            </div>
            <div class="tooth-row-label">Lower Left</div>
            <div class="tooth-row" id="lowerLeft">
              <?php foreach([31,32,33,34,35,36,37,38] as $t): ?>
              <div class="tooth" data-num="<?= $t ?>" onclick="toggleTooth(<?= $t ?>)"><?= $t ?></div>
              <?php endforeach; ?>
            </div>
          </div>

          <input type="hidden" name="teeth" id="teethInput" value="<?= clean($record['teeth'] ?? '') ?>">

          <div style="margin-top:12px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <span style="font-size:0.78rem;color:var(--text-muted)">Selected:</span>
            <div id="selectedTeeth" style="display:flex;gap:4px;flex-wrap:wrap">
              <span style="font-size:0.78rem;color:var(--text-muted)" id="noTeeth">None</span>
            </div>
            <button type="button" onclick="clearTeeth()" class="btn btn-outline" style="padding:3px 10px;font-size:0.75rem;margin-left:auto">Clear</button>
          </div>
        </div>
      </div>

    </div>

    <!-- ===== RIGHT ===== -->
    <div class="rec-form-aside">

      <!-- Procedure + Dentist -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:6px"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Treatment Details
          </span>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label class="form-label">Procedure <span style="color:var(--danger)">*</span></label>
            <select name="procedure_id" id="procedureSelect" class="form-select" required>
              <option value="">Select procedure...</option>
              <?php foreach($byCategory as $cat => $procs): ?>
              <optgroup label="<?= clean($cat) ?>">
                <?php foreach($procs as $p): ?>
                <option value="<?= $p['id'] ?>"
                  data-color="<?= clean($p['color']) ?>"
                  data-price="<?= $p['price'] ?>"
                  data-duration="<?= $p['duration'] ?>"
                  <?= ($record['procedure_id'] ?? 0) == $p['id'] ? 'selected' : '' ?>>
                  <?= clean($p['name']) ?> — <?= formatMoney($p['price']) ?>
                </option>
                <?php endforeach; ?>
              </optgroup>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Dentist <span style="color:var(--danger)">*</span></label>
            <select name="dentist_id" class="form-select" required>
              <option value="">Select dentist...</option>
              <?php foreach($dentists as $d): ?>
              <option value="<?= $d['id'] ?>"
                style="border-left:3px solid <?= clean($d['color']) ?>"
                <?= ($record['dentist_id'] ?? 0) == $d['id'] ? 'selected' : '' ?>>
                <?= clean($d['name']) ?> — <?= clean($d['specialty']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Actual Duration (minutes)</label>
            <input type="number" name="duration" class="form-input" id="durationInput"
              placeholder="60" min="1" max="480"
              value="<?= clean((string)($record['duration'] ?? '')) ?>">
          </div>
        </div>
      </div>

      <!-- Procedure info card (dynamic) -->
      <div class="card" id="procInfoCard" style="display:none">
        <div class="card-body" style="padding:14px 18px">
          <div style="font-size:0.72rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px">Procedure Info</div>
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div>
              <div style="font-size:0.8rem;color:var(--text-secondary)">Standard price</div>
              <div style="font-size:1.1rem;font-weight:600;color:var(--primary)" id="procPrice">—</div>
            </div>
            <div style="text-align:right">
              <div style="font-size:0.8rem;color:var(--text-secondary)">Est. duration</div>
              <div style="font-size:1.1rem;font-weight:600" id="procDuration">—</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Submit -->
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <a href="<?= $isEdit ? url('records/show?id='.$record['id']) : url('records') ?>" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
          <?= $isEdit ? 'Save Changes' : 'Create Record' ?>
        </button>
      </div>

    </div>
  </div>
</form>
