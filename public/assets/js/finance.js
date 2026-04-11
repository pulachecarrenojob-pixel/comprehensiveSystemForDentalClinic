/* ============================================================
   DentalCare — finance.js
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
  const d = window.financeData;
  if (!d) return;

  const gridColor = 'rgba(0,0,0,0.05)';
  const textColor = '#6b7280';
  const baseFont  = { family: "'DM Sans', system-ui, sans-serif", size: 12 };
  const tipStyle  = {
    backgroundColor: '#111827',
    titleColor: '#fff',
    bodyColor: '#9ca3af',
    padding: 10,
    cornerRadius: 8,
    displayColors: false,
  };

  // ---- Daily revenue bar chart ----
  const dailyCtx = document.getElementById('chartDaily');
  if (dailyCtx) {
    new Chart(dailyCtx, {
      type: 'bar',
      data: {
        labels: d.dailyLabels.length ? d.dailyLabels : ['No data'],
        datasets: [{
          label: 'Revenue',
          data: d.dailyData.length ? d.dailyData : [0],
          backgroundColor: d.dailyData.map(v =>
            v === Math.max(...d.dailyData) && v > 0 ? '#1D9E75' : 'rgba(29,158,117,0.2)'
          ),
          borderRadius: 6,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { display: false },
          tooltip: { ...tipStyle, callbacks: {
            label: (item) => ' S/ ' + Number(item.raw).toLocaleString('es-PE', { minimumFractionDigits: 2 }),
          }}
        },
        scales: {
          x: { grid: { display: false }, ticks: { color: textColor, font: baseFont } },
          y: {
            beginAtZero: true,
            grid: { color: gridColor },
            border: { display: false },
            ticks: {
              color: textColor,
              font: baseFont,
              callback: v => 'S/ ' + Number(v).toLocaleString(),
            }
          }
        }
      }
    });
  }

  // ---- Payment methods donut ----
  const methodCtx = document.getElementById('chartMethods');
  if (methodCtx && d.methodData.length > 0) {
    new Chart(methodCtx, {
      type: 'doughnut',
      data: {
        labels: d.methodLabels,
        datasets: [{
          data: d.methodData,
          backgroundColor: d.methodColors,
          borderWidth: 2,
          borderColor: '#fff',
          hoverOffset: 6,
        }]
      },
      options: {
        responsive: true,
        cutout: '65%',
        plugins: {
          legend: { display: false },
          tooltip: { ...tipStyle, callbacks: {
            label: (item) => ' S/ ' + Number(item.raw).toLocaleString('es-PE', { minimumFractionDigits: 2 }),
          }}
        }
      }
    });
  }

  // ---- Client-side search ----
  const searchInput = document.getElementById('txSearch');
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const q = searchInput.value.toLowerCase().trim();
      document.querySelectorAll('.tx-row').forEach(row => {
        const name = (row.dataset.search || '').toLowerCase();
        row.style.display = (!q || name.includes(q)) ? '' : 'none';
      });
    });
  }
});

// ---- Payment modal autofill ----
function fillPaymentForm(select) {
  const opt     = select.options[select.selectedIndex];
  const info    = document.getElementById('apptInfo');
  const nameEl  = document.getElementById('apptPatientName');
  const procEl  = document.getElementById('apptProcName');
  const amount  = document.getElementById('amountInput');
  const patient = document.getElementById('patientIdHidden');

  if (!opt.value) {
    if (info) info.style.display = 'none';
    return;
  }

  if (info)    info.style.display    = 'block';
  if (nameEl)  nameEl.textContent    = opt.dataset.name  || '';
  if (procEl)  procEl.textContent    = opt.dataset.proc  || '';
  if (amount)  amount.value          = opt.dataset.price || '';
  if (patient) patient.value         = opt.dataset.patient || '0';
}

// ---- Server-side filters ----
function applyFilters() {
  const status = document.getElementById('statusFilter')?.value || '';
  const method = document.getElementById('methodFilter')?.value || '';
  const search = document.getElementById('txSearch')?.value    || '';
  const params = new URLSearchParams({ url: 'finance' });
  if (status) params.set('status', status);
  if (method) params.set('method', method);
  if (search) params.set('search', search);
  window.location.href = window.location.pathname + '?' + params.toString();
}
