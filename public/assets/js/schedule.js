/* ============================================================
   DentalCare — schedule.js
   Calendar interactions, quick book, appointment view
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {

  // ---- Dentist filter ----
  document.querySelectorAll('.dentist-filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.dentist-filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const dentistId = btn.dataset.dentist;
      document.querySelectorAll('.cal-appt').forEach(appt => {
        if (dentistId === 'all' || appt.dataset.dentist === dentistId) {
          appt.style.display = '';
        } else {
          appt.style.display = 'none';
        }
      });
    });
  });

  // ---- Scroll to 08:00 on load ----
  const wrap = document.querySelector('.cal-wrap');
  if (wrap) {
    const slotH = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--slot-h')) || 40;
    wrap.scrollTop = slotH * 2; // scroll to 09:00
  }

  // ---- End time auto-update ----
  updateEndTime();
});

// Quick book: click on empty slot
function quickBook(date, time) {
  const dateInput = document.getElementById('apptDate');
  const startInput = document.getElementById('apptStart');
  if (dateInput) dateInput.value = date;
  if (startInput) {
    startInput.value = time + ':00';
    updateEndTime();
  }
  openModal('newApptModal');
}

// Auto-calculate end time from procedure duration
function updateEndTime() {
  const procSelect = document.getElementById('apptProcedure');
  const startInput = document.getElementById('apptStart');
  const endInput   = document.getElementById('apptEnd');
  if (!procSelect || !startInput || !endInput) return;

  const selected = procSelect.options[procSelect.selectedIndex];
  const duration = parseInt(selected?.dataset?.duration || 60);
  const startVal = startInput.value;
  if (!startVal) return;

  const [h, m] = startVal.split(':').map(Number);
  const startMins = h * 60 + m;
  const endMins   = startMins + duration;
  const endH = Math.floor(endMins / 60);
  const endM = endMins % 60;
  endInput.value = `${String(endH).padStart(2,'0')}:${String(endM).padStart(2,'0')}`;
}

// View appointment details
function viewAppt(appt) {
  document.getElementById('vApptId').value       = appt.id;
  document.getElementById('vApptCancelId').value = appt.id;
  document.getElementById('vApptTitle').textContent = appt.patient_name;

  const statusSelect = document.getElementById('vApptStatus');
  if (statusSelect) statusSelect.value = appt.status;

  const statusColors = {
    scheduled: '#378ADD', confirmed: '#1D9E75',
    completed: '#534AB7', no_show: '#EF9F27', cancelled: '#E24B4A'
  };

  document.getElementById('vApptBody').innerHTML = `
    <div class="appt-detail-row">
      <div class="appt-detail-icon">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
      <div>
        <div class="appt-detail-label">Date & Time</div>
        <div class="appt-detail-value">${formatDisplayDate(appt.date)} · ${appt.start_time.slice(0,5)} – ${appt.end_time.slice(0,5)}</div>
      </div>
    </div>
    <div class="appt-detail-row">
      <div class="appt-detail-icon" style="background:${appt.dentist_color}20;color:${appt.dentist_color}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div>
        <div class="appt-detail-label">Dentist</div>
        <div class="appt-detail-value">${appt.dentist_name}</div>
      </div>
    </div>
    <div class="appt-detail-row">
      <div class="appt-detail-icon" style="background:${appt.procedure_color}20;color:${appt.procedure_color}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      </div>
      <div>
        <div class="appt-detail-label">Procedure</div>
        <div class="appt-detail-value">${appt.procedure_name}</div>
      </div>
    </div>
    <div class="appt-detail-row">
      <div class="appt-detail-icon">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div>
        <div class="appt-detail-label">Status</div>
        <div class="appt-detail-value" style="color:${statusColors[appt.status] || '#888'}">${appt.status.charAt(0).toUpperCase() + appt.status.slice(1)}</div>
      </div>
    </div>
    ${appt.notes ? `
    <div class="appt-detail-row">
      <div class="appt-detail-icon">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
      </div>
      <div>
        <div class="appt-detail-label">Notes</div>
        <div class="appt-detail-value" style="font-weight:400;color:var(--text-secondary)">${appt.notes}</div>
      </div>
    </div>` : ''}
  `;

  openModal('viewApptModal');
}

function formatDisplayDate(dateStr) {
  const d = new Date(dateStr + 'T00:00:00');
  return d.toLocaleDateString('en-GB', { weekday:'short', day:'numeric', month:'short', year:'numeric' });
}
