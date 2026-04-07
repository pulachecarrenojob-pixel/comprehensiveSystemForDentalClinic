<?php
// Reusable allergy row partial
// Variables: $al (array with 'name' and 'severity') OR empty for blank row
$alName     = $al['name']     ?? '';
$alSeverity = $al['severity'] ?? 'mild';
?>
<div class="allergy-row" style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
  <input type="text" name="allergy_name[]" class="form-input" placeholder="e.g. Penicillin"
    value="<?= clean($alName) ?>" style="flex:1">
  <select name="allergy_severity[]" class="form-select" style="width:110px;flex-shrink:0">
    <option value="mild"     <?= $alSeverity==='mild'     ? 'selected':'' ?>>Mild</option>
    <option value="moderate" <?= $alSeverity==='moderate' ? 'selected':'' ?>>Moderate</option>
    <option value="severe"   <?= $alSeverity==='severe'   ? 'selected':'' ?>>Severe</option>
  </select>
  <button type="button" class="action-btn danger" onclick="removeRow(this)" title="Remove">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>
</div>
