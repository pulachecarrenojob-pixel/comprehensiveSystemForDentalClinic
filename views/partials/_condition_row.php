<?php
// Reusable condition row partial
// Variable: $c (string condition name) OR empty for blank row
$cName = is_array($c ?? null) ? ($c['name'] ?? '') : ($c ?? '');
?>
<div class="condition-row" style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
  <input type="text" name="conditions[]" class="form-input" placeholder="e.g. Hypertension"
    value="<?= clean($cName) ?>" style="flex:1">
  <button type="button" class="action-btn danger" onclick="removeRow(this)" title="Remove">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>
</div>
