/* ============================================================
   DentalCare — patients.js
   Real-time search and delete confirmation
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {

  const searchInput = document.getElementById('searchInput');
  const tbody       = document.getElementById('patientsTbody');
  const countEl     = document.getElementById('resultCount');
  let searchTimer   = null;

  if (searchInput) {
    searchInput.addEventListener('input', () => {
      clearTimeout(searchTimer);
      const q = searchInput.value.trim();

      // Client-side filter for speed
      if (tbody) {
        const rows = tbody.querySelectorAll('.patient-row');
        let visible = 0;
        rows.forEach(row => {
          const name = (row.dataset.name || '').toLowerCase();
          const text = row.textContent.toLowerCase();
          const match = !q || text.includes(q.toLowerCase());
          row.style.display = match ? '' : 'none';
          if (match) visible++;
        });
        if (countEl) countEl.textContent = visible + ' patients';
        return;
      }

      // Server-side search fallback
      searchTimer = setTimeout(() => {
        const base = window.location.pathname;
        window.location.href = base + '?url=patients&search=' + encodeURIComponent(q);
      }, 600);
    });

    // Focus on load if no results
    if (tbody && tbody.querySelectorAll('.patient-row').length === 0) {
      searchInput.focus();
    }
  }
});

function confirmDelete(id, name) {
  document.getElementById('deleteId').value = id;
  document.getElementById('deletePatientName').textContent = name;
  openModal('deleteModal');
}
