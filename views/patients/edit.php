<?php $isEdit = isset($patient); ?>

<div class="page-header">
  <div class="page-header-left">
    <h2><?= $isEdit ? 'Edit Patient' : 'New Patient' ?></h2>
    <p><?= $isEdit ? 'Update patient information' : 'Register a new patient in the system' ?></p>
  </div>
  <a href="<?= $isEdit ? url('patients/show?id='.$patient['id']) : url('patients') ?>" class="btn btn-outline">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
    Back
  </a>
</div>

<form method="POST" action="<?= $isEdit ? url('patients/update') : url('patients/store') ?>">
  <?= csrfField() ?>
  <?php if($isEdit): ?>
  <input type="hidden" name="id" value="<?= $patient['id'] ?>">
  <?php endif; ?>

  <div class="grid-2" style="gap:20px;align-items:start">

    <!-- Personal info -->
    <div style="display:flex;flex-direction:column;gap:20px">
      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:6px"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Personal Information
          </span>
        </div>
        <div class="card-body">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">First Name <span style="color:var(--danger)">*</span></label>
              <input type="text" name="first_name" class="form-input"
                placeholder="Ana"
                value="<?= clean($patient['first_name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Last Name <span style="color:var(--danger)">*</span></label>
              <input type="text" name="last_name" class="form-input"
                placeholder="Torres"
                value="<?= clean($patient['last_name'] ?? '') ?>" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Date of Birth</label>
              <input type="date" name="birth_date" class="form-input"
                value="<?= clean($patient['birth_date'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Gender</label>
              <select name="gender" class="form-select">
                <option value="male"   <?= ($patient['gender'] ?? '') === 'male'   ? 'selected' : '' ?>>Male</option>
                <option value="female" <?= ($patient['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                <option value="other"  <?= ($patient['gender'] ?? '') === 'other'  ? 'selected' : '' ?>>Other</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">ID / Document Number</label>
            <input type="text" name="id_number" class="form-input"
              placeholder="12345678"
              value="<?= clean($patient['id_number'] ?? '') ?>">
          </div>
        </div>
      </div>

      <!-- Contact -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:6px"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.41 2 2 0 0 1 3.6 1.23h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.82a16 16 0 0 0 6.27 6.27l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            Contact
          </span>
        </div>
        <div class="card-body">
          <div class="form-group">
            <label class="form-label">Phone <span style="color:var(--danger)">*</span></label>
            <input type="tel" name="phone" class="form-input"
              placeholder="555-0000"
              value="<?= clean($patient['phone'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input"
              placeholder="patient@email.com"
              value="<?= clean($patient['email'] ?? '') ?>">
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-input"
              placeholder="Av. Example 123, Lima"
              value="<?= clean($patient['address'] ?? '') ?>">
          </div>
        </div>
      </div>
    </div>

    <!-- Right column -->
    <div style="display:flex;flex-direction:column;gap:20px">

      <!-- Insurance -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:6px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Insurance Plan
          </span>
        </div>
        <div class="card-body">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Insurance</label>
            <select name="insurance_id" class="form-select">
              <option value="">No insurance</option>
              <?php foreach($insurance as $ins): ?>
              <option value="<?= $ins['id'] ?>"
                <?= ($patient['insurance_id'] ?? '') == $ins['id'] ? 'selected' : '' ?>>
                <?= clean($ins['name']) ?> (<?= $ins['coverage'] ?>% coverage)
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <!-- Notes -->
      <div class="card">
        <div class="card-header">
          <span class="card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:6px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Notes
          </span>
        </div>
        <div class="card-body">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Additional notes</label>
            <textarea name="notes" class="form-textarea" rows="5"
              placeholder="Any relevant notes about this patient..."><?= clean($patient['notes'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <!-- Submit -->
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <a href="<?= $isEdit ? url('patients/show?id='.$patient['id']) : url('patients') ?>" class="btn btn-outline">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
          <?= $isEdit ? 'Save Changes' : 'Create Patient' ?>
        </button>
      </div>

    </div>
  </div>
</form>
