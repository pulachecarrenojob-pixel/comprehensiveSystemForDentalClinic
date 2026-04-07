/* ============================================================
   DentalCare — anamnesis.js
   Dynamic allergy/condition rows, quick-add chips, search
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {

  // Client-side search
  const searchInput = document.getElementById('searchInput');
  const countEl     = document.getElementById('resultCount');
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const q = searchInput.value.toLowerCase().trim();
      const rows = document.querySelectorAll('.ana-row');
      let visible = 0;
      rows.forEach(row => {
        const name = (row.dataset.name || '').toLowerCase();
        const show = !q || name.includes(q);
        row.style.display = show ? '' : 'none';
        if (show) visible++;
      });
      if (countEl) countEl.textContent = visible + ' records';
    });
  }
});

// ---- Dynamic row: allergy ----
function addAllergyRow(name = '', severity = 'mild') {
  const list = document.getElementById('allergyList');
  const none = document.getElementById('noAllergies');
  if (!list) return;
  if (none) none.style.display = 'none';

  const row = document.createElement('div');
  row.className = 'allergy-row';
  row.style.cssText = 'display:flex;gap:8px;align-items:center;margin-bottom:8px';
  row.innerHTML = `
    <input type="text" name="allergy_name[]" class="form-input" placeholder="e.g. Penicillin"
      value="${escHtml(name)}" style="flex:1">
    <select name="allergy_severity[]" class="form-select" style="width:110px;flex-shrink:0">
      <option value="mild"     ${severity==='mild'     ?'selected':''}>Mild</option>
      <option value="moderate" ${severity==='moderate' ?'selected':''}>Moderate</option>
      <option value="severe"   ${severity==='severe'   ?'selected':''}>Severe</option>
    </select>
    <button type="button" class="action-btn danger" onclick="removeRow(this)" title="Remove">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
  `;
  list.appendChild(row);
  row.querySelector('input').focus();
}

function quickAddAllergy(name) {
  // Avoid duplicates
  const inputs = document.querySelectorAll('#allergyList input[name="allergy_name[]"]');
  for (const inp of inputs) {
    if (inp.value.toLowerCase() === name.toLowerCase()) {
      inp.closest('.allergy-row').style.outline = '2px solid var(--primary)';
      setTimeout(() => inp.closest('.allergy-row').style.outline = '', 1200);
      return;
    }
  }
  addAllergyRow(name, 'mild');
}

// ---- Dynamic row: condition ----
function addConditionRow(name = '') {
  const list = document.getElementById('conditionList');
  const none = document.getElementById('noConditions');
  if (!list) return;
  if (none) none.style.display = 'none';

  const row = document.createElement('div');
  row.className = 'condition-row';
  row.style.cssText = 'display:flex;gap:8px;align-items:center;margin-bottom:8px';
  row.innerHTML = `
    <input type="text" name="conditions[]" class="form-input" placeholder="e.g. Hypertension"
      value="${escHtml(name)}" style="flex:1">
    <button type="button" class="action-btn danger" onclick="removeRow(this)" title="Remove">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
  `;
  list.appendChild(row);
  row.querySelector('input').focus();
}

function quickAddCondition(name) {
  const inputs = document.querySelectorAll('#conditionList input[name="conditions[]"]');
  for (const inp of inputs) {
    if (inp.value.toLowerCase() === name.toLowerCase()) {
      inp.closest('.condition-row').style.outline = '2px solid var(--warning)';
      setTimeout(() => inp.closest('.condition-row').style.outline = '', 1200);
      return;
    }
  }
  addConditionRow(name);
}

// ---- Generic remove row ----
function removeRow(btn) {
  const row = btn.closest('[class$="-row"]');
  if (!row) return;
  row.style.opacity = '0';
  row.style.transform = 'translateX(10px)';
  row.style.transition = 'all 0.2s ease';
  setTimeout(() => {
    row.remove();
    updateEmptyState('allergyList',   'noAllergies');
    updateEmptyState('conditionList', 'noConditions');
  }, 200);
}

function updateEmptyState(listId, noneId) {
  const list = document.getElementById(listId);
  const none = document.getElementById(noneId);
  if (!list || !none) return;
  none.style.display = list.children.length === 0 ? 'block' : 'none';
}

function escHtml(str) {
  return String(str)
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;');
}
