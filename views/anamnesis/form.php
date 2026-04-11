<?php
$isEdit = !empty($anamnesis);
$action = $isEdit ? url('anamnesis/update') : url('anamnesis/store');

// Blood type options
$bloodTypes = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];

// Severity options for allergies
$severities = ['mild' => 'Mild', 'moderate' => 'Moderate', 'severe' => 'Severe'];

// Common conditions for quick-add chips
$commonConditions = [
    'Hypertension','Diabetes Type 1','Diabetes Type 2','Asthma',
    'Heart Disease','Thyroid Disease','Epilepsy','HIV/AIDS',
    'Hepatitis B','Hepatitis C','Kidney Disease','Cancer',
];

// Common allergies for quick-add
$commonAllergies = [
    'Penicillin','Amoxicillin','Aspirin','Ibuprofen',
    'Latex','Lidocaine','Codeine','Sulfa drugs',
];
?>

<div class="page-header">
  <div class="page-header-left">
    <h2><?= $isEdit ? 'Edit Anamnesis' : 'New Anamnesis' ?></h2>
    <p><?= $isEdit ? 'Update  medical history for '  .clean($anamnesis['patient_name'] ?? '') : 'Create a new medical history record' ?></p>
  </div>
  <a href="<?= $isEdit ? url('anamnesis/show?id='.$anamnesis['id']) : url('anamnesis') ?>" class="btn btn-outline">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Back
  </a>
</div>

<form method="POST" action="<?= $action ?>" id="anamnesisForm">
  <?= csrfField() ?>
  <?php if($isEdit): ?>
  <input type="hidden" name="id" value="<?= $anamnesis['id'] ?>">
  <?php endif; ?>

  <div class="ana-form-layout">

    <!-- ===== LEFT: Main data ===== -->
    <div class="ana-form-main">

      <!-- Patient selector (only on create) -->
      <?php if(!$isEdit): ?>
      <div class="card">
        <div class="card-header">
          <?php include __DIR__.'/../partials/_section_title.php'; echo sectionTitle('user','Patient'); ?>
        </div>
        <div class="card-body">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Select Patient <span class="required">*</span></label>
            <select name="patient_id" class="form-select" required>
              <option value="">Choose a patient...</option>
              <?php foreach($patients as $p): ?>
              <option value="<?= $p['id'] ?>" <?= ($preselect == $p['id'] || ($anamnesis['patient_id'] ?? 0) == $p['id']) ? 'selected' : '' ?>>
                <?= clean($p['full_name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>
      <?php else: ?>
      <input type="hidden" name="patient_id" value="<?= $anamnesis['patient_id'] ?>">
      <?php endif; ?>

      <!-- Chief complaint -->
      <div class="card">
        <div class="card-header">
          <?php echo sectionTitle('message-square','Chief Complaint'); ?>
        </div>
        <div class="card-body">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Main reason for visit <span class="required">*</span></label>
            <textarea name="chief_complaint" class="form-textarea" rows="3"
              placeholder="Describe the patient's main complaint or reason for the visit..."
              required><?= clean($anamnesis['chief_complaint'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <!-- Medical history & medications -->
      <div class="card">
        <div class="card-header">
          <?php echo sectionTitle('file-text','Medical History'); ?>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label class="form-label">Past medical history</label>
            <textarea name="medical_history" class="form-textarea" rows="3"
              placeholder="Previous surgeries, hospitalizations, chronic diseases..."><?= clean($anamnesis['medical_history'] ?? '') ?></textarea>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Current medications</label>
            <textarea name="current_meds" class="form-textarea" rows="3"
              placeholder="List all medications the patient is currently taking..."><?= clean($anamnesis['current_meds'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <!-- Additional notes -->
      <div class="card">
        <div class="card-header">
          <?php echo sectionTitle('edit-3','Additional Notes'); ?>
        </div>
        <div class="card-body">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-textarea" rows="3"
              placeholder="Any other relevant clinical notes..."><?= clean($anamnesis['notes'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

    </div>

    <!-- ===== RIGHT: Extra data ===== -->
    <div class="ana-form-aside">

      <!-- Vital info -->
      <div class="card">
        <div class="card-header">
          <?php echo sectionTitle('activity','Vital Information'); ?>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label class="form-label">Blood Type</label>
            <select name="blood_type" class="form-select">
              <option value="">Unknown</option>
              <?php foreach($bloodTypes as $bt): ?>
              <option value="<?= $bt ?>" <?= ($anamnesis['blood_type'] ?? '') === $bt ? 'selected' : '' ?>>
                <?= $bt ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="toggle-group">
            <label class="toggle-label">
              <div class="toggle-info">
                <span class="toggle-title">Smoker</span>
                <span class="toggle-desc">Patient is a regular smoker</span>
              </div>
              <div class="toggle-switch">
                <input type="checkbox" name="smoker" value="1" id="smokerToggle"
                  <?= !empty($anamnesis['smoker']) ? 'checked' : '' ?>>
                <span class="toggle-track-el"></span>
              </div>
            </label>
          </div>

          <div class="toggle-group" style="margin-bottom:0">
            <label class="toggle-label">
              <div class="toggle-info">
                <span class="toggle-title">Pregnant</span>
                <span class="toggle-desc">Patient is currently pregnant</span>
              </div>
              <div class="toggle-switch">
                <input type="checkbox" name="pregnant" value="1" id="pregnantToggle"
                  <?= !empty($anamnesis['pregnant']) ? 'checked' : '' ?>>
                <span class="toggle-track-el"></span>
              </div>
            </label>
          </div>
        </div>
      </div>

      <!-- Allergies -->
      <div class="card">
        <div class="card-header">
          <?php echo sectionTitle('alert-triangle','Allergies'); ?>
          <button type="button" class="btn btn-outline" style="padding:4px 10px;font-size:0.78rem" onclick="addAllergyRow()">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add
          </button>
        </div>
        <div class="card-body">
          <!-- Quick add chips -->
          <div class="chip-group" style="margin-bottom:14px">
            <?php foreach($commonAllergies as $ca): ?>
            <button type="button" class="chip" onclick="quickAddAllergy('<?= $ca ?>')">+ <?= $ca ?></button>
            <?php endforeach; ?>
          </div>

          <div id="allergyList">
            <?php if($allergies): ?>
              <?php foreach($allergies as $al): ?>
              <?php include __DIR__.'/../partials/_allergy_row.php'; ?>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <div id="noAllergies" style="<?= $allergies ? 'display:none' : '' ?>;text-align:center;padding:16px;color:var(--text-muted);font-size:0.82rem">
            No allergies added yet
          </div>
        </div>
      </div>

      <!-- Medical conditions -->
      <div class="card">
        <div class="card-header">
          <?php echo sectionTitle('heart','Medical Conditions'); ?>
          <button type="button" class="btn btn-outline" style="padding:4px 10px;font-size:0.78rem" onclick="addConditionRow()">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add
          </button>
        </div>
        <div class="card-body">
          <!-- Quick add chips -->
          <div class="chip-group" style="margin-bottom:14px">
            <?php foreach($commonConditions as $cc): ?>
            <button type="button" class="chip chip-warning" onclick="quickAddCondition('<?= $cc ?>')">+ <?= $cc ?></button>
            <?php endforeach; ?>
          </div>

          <div id="conditionList">
            <?php if($conditions): ?>
              <?php foreach($conditions as $c): ?>
              <?php include __DIR__.'/../partials/_condition_row.php'; ?>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <div id="noConditions" style="<?= $conditions ? 'display:none' : '' ?>;text-align:center;padding:16px;color:var(--text-muted);font-size:0.82rem">
            No conditions added yet
          </div>
        </div>
      </div>

      <!-- Submit -->
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <a href="<?= $isEdit ? url('anamnesis/show?id='.$anamnesis['id']) : url('anamnesis') ?>" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          <?= $isEdit ? 'Save Changes' : 'Create Anamnesis' ?>
        </button>
      </div>

    </div>
  </div>
</form>

<?php
// ---- Reusable section title helper ----
function sectionTitle(string $icon, string $label): string {
    $icons = [
        'user'           => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'message-square' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        'file-text'      => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'edit-3'         => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>',
        'activity'       => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
        'alert-triangle' => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>',
        'heart'          => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
    ];
    return '<span class="card-title"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:6px">'
         . ($icons[$icon] ?? '') . '</svg>' . $label . '</span>';
}
?>
