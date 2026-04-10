/* ============================================================
   DentalCare — records.js
   Tooth chart, search, dynamic appointment loader
   ============================================================ */

// ---- Tooth selection state ----
let selectedTeeth = new Set();

document.addEventListener('DOMContentLoaded', () => {

  // Init teeth from hidden input (edit mode)
  const teethInput = document.getElementById('teethInput');
  if (teethInput && teethInput.value) {
    teethInput.value.split(',').forEach(t => {
      const num = parseInt(t.trim());
      if (num) {
        selectedTeeth.add(num);
        const el = document.querySelector(`.tooth[data-num="${num}"]`);
        if (el) el.classList.add('selected');
      }
    });
    renderSelectedTeeth();
  }

  // Procedure info card
  const procSelect = document.getElementById('procedureSelect');
  if (procSelect) {
    procSelect.addEventListener('change', () => {
      const opt = procSelect.options[procSelect.selectedIndex];
      const price    = opt.dataset.price;
      const duration = opt.dataset.duration;
      const card     = document.getElementById('procInfoCard');
      if (!opt.value) { card.style.display = 'none'; return; }
      card.style.display = 'block';
      document.getElementById('procPrice').textContent    = price    ? 'S/ ' + parseFloat(price).toFixed(2) : '—';
      document.getElementById('procDuration').textContent = duration ? duration + ' min' : '—';
      // Pre-fill duration input
      const durInput = document.getElementById('durationInput');
      if (durInput && !durInput.value) durInput.value = duration || '';
    });
  }

  // Client-side search
  const searchInput = document.getElementById('searchInput');
  const countEl     = document.getElementById('resultCount');
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const q = searchInput.value.toLowerCase().trim();
      const rows = document.querySelectorAll('.rec-row');
      let visible = 0;
      rows.forEach(row => {
        const text = (row.dataset.search || '').toLowerCase();
        const show = !q || text.includes(q);
        row.style.display = show ? '' : 'none';
        if (show) visible++;
      });
      if (countEl) countEl.textContent = visible + ' records';
    });
  }
});

// ---- Tooth chart ----
function toggleTooth(num) {
  const el = document.querySelector(`.tooth[data-num="${num}"]`);
  if (!el) return;
  if (selectedTeeth.has(num)) {
    selectedTeeth.delete(num);
    el.classList.remove('selected');
  } else {
    selectedTeeth.add(num);
    el.classList.add('selected');
  }
  renderSelectedTeeth();
  syncTeethInput();
}

function renderSelectedTeeth() {
  const container = document.getElementById('selectedTeeth');
  const noTeeth   = document.getElementById('noTeeth');
  if (!container) return;

  container.innerHTML = '';
  const sorted = [...selectedTeeth].sort((a, b) => a - b);

  if (sorted.length === 0) {
    if (noTeeth) { noTeeth.style.display = 'inline'; container.appendChild(noTeeth); }
    return;
  }
  if (noTeeth) noTeeth.style.display = 'none';

  sorted.forEach(num => {
    const badge = document.createElement('span');
    badge.className = 'tooth-badge';
    badge.textContent = num;
    badge.style.cursor = 'pointer';
    badge.title = 'Click to deselect';
    badge.onclick = () => toggleTooth(num);
    container.appendChild(badge);
  });
}

function syncTeethInput() {
  const input = document.getElementById('teethInput');
  if (input) {
    input.value = [...selectedTeeth].sort((a, b) => a - b).join(',');
  }
}

function clearTeeth() {
  selectedTeeth.clear();
  document.querySelectorAll('.tooth.selected').forEach(el => el.classList.remove('selected'));
  renderSelectedTeeth();
  syncTeethInput();
}

// ---- Load appointments by patient (AJAX) ----
function loadAppointments(patientId) {
  const select = document.getElementById('appointmentSelect');
  if (!select) return;
  select.innerHTML = '<option value="">Loading...</option>';

  if (!patientId) {
    select.innerHTML = '<option value="">Select patient first</option>';
    return;
  }

  fetch(`?url=api/patients/search&q=&patient_id=${patientId}`)
    .then(() => {
      // Reload page with patient preselected to get appointments
      window.location.href = `?url=records/create&patient_id=${patientId}`;
    })
    .catch(() => {
      select.innerHTML = '<option value="">Could not load appointments</option>';
    });
}

// ---- Patient filter in index ----
function filterByPatient(patientId) {
  const base = window.location.pathname;
  window.location.href = base + '?url=records&patient_id=' + patientId;
}
