/* ============================================================
   DentalCare — reports.js
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
  const d  = window.reportsData;
  const cy = window.reportsCurrency || 'S/';
  if (!d) return;

  const textColor = '#6b7280';
  const gridColor = 'rgba(0,0,0,0.05)';
  const baseFont  = { family: "'DM Sans', system-ui, sans-serif", size: 12 };
  const tipStyle  = {
    backgroundColor: '#111827',
    titleColor: '#fff',
    bodyColor: '#9ca3af',
    padding: 10,
    cornerRadius: 8,
    displayColors: true,
  };

  const scaleDefaults = (yLabel) => ({
    x: { grid: { display: false }, ticks: { color: textColor, font: baseFont } },
    y: {
      beginAtZero: true,
      grid: { color: gridColor },
      border: { display: false },
      ticks: { color: textColor, font: baseFont,
        callback: v => yLabel ? (yLabel + ' ' + Number(v).toLocaleString()) : v
      }
    }
  });

  // ---- Bar: appointments by dentist ----
  const apptCtx = document.getElementById('chartDentistAppts');
  if (apptCtx && d.dentistLabels.length) {
    new Chart(apptCtx, {
      type: 'bar',
      data: {
        labels: d.dentistLabels,
        datasets: [{
          label: 'Appointments',
          data: d.dentistAppts,
          backgroundColor: d.dentistColors.length ? d.dentistColors : '#1D9E75',
          borderRadius: 6,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: { ...tipStyle }},
        scales: scaleDefaults(null),
      }
    });
  }

  // ---- Bar: revenue by dentist ----
  const revCtx = document.getElementById('chartDentistRev');
  if (revCtx && d.revLabels.length) {
    new Chart(revCtx, {
      type: 'bar',
      data: {
        labels: d.revLabels,
        datasets: [{
          label: 'Revenue',
          data: d.revData,
          backgroundColor: d.revColors.length ? d.revColors : '#378ADD',
          borderRadius: 6,
          borderSkipped: false,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: { ...tipStyle, callbacks: {
            label: item => ' ' + cy + ' ' + Number(item.raw).toLocaleString('es-PE', {minimumFractionDigits:2})
          }}
        },
        scales: scaleDefaults(cy),
      }
    });
  }

  // ---- Line: monthly evolution (dual axis) ----
  const evoCtx = document.getElementById('chartEvolution');
  if (evoCtx && d.evoLabels.length) {
    new Chart(evoCtx, {
      type: 'line',
      data: {
        labels: d.evoLabels,
        datasets: [
          {
            label: 'Appointments',
            data: d.evoAppts,
            borderColor: '#1D9E75',
            backgroundColor: 'rgba(29,158,117,0.07)',
            borderWidth: 2.5,
            pointBackgroundColor: '#1D9E75',
            pointRadius: 4,
            tension: 0.4,
            fill: true,
            yAxisID: 'y',
          },
          {
            label: 'Revenue',
            data: d.evoRevenue,
            borderColor: '#378ADD',
            backgroundColor: 'rgba(55,138,221,0.07)',
            borderWidth: 2.5,
            pointBackgroundColor: '#378ADD',
            pointRadius: 4,
            tension: 0.4,
            fill: true,
            yAxisID: 'y1',
          }
        ]
      },
      options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: { ...tipStyle, callbacks: {
            label: item => item.datasetIndex === 1
              ? ' ' + cy + ' ' + Number(item.raw).toLocaleString('es-PE', {minimumFractionDigits:2})
              : ' ' + item.raw + ' appointments'
          }}
        },
        scales: {
          x: { grid: { display: false }, ticks: { color: textColor, font: baseFont } },
          y: {
            type: 'linear', position: 'left',
            beginAtZero: true,
            grid: { color: gridColor },
            border: { display: false },
            ticks: { color: '#1D9E75', font: baseFont, callback: v => v }
          },
          y1: {
            type: 'linear', position: 'right',
            beginAtZero: true,
            grid: { drawOnChartArea: false },
            border: { display: false },
            ticks: { color: '#378ADD', font: baseFont, callback: v => cy+' '+v.toLocaleString() }
          }
        }
      }
    });
  }
});
