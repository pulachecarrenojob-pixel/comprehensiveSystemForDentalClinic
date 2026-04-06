/* ============================================================
   DentalCare — dashboard.js
   Chart.js charts for the dashboard
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
  const d = window.dashboardData;
  if (!d) return;

  const primaryColor  = '#1D9E75';
  const accentColor   = '#378ADD';
  const gridColor     = 'rgba(0,0,0,0.05)';
  const textColor     = '#6b7280';

  const baseFont = { family: "'DM Sans', system-ui, sans-serif", size: 12 };

  // Shared tooltip style
  const tooltipStyle = {
    backgroundColor: '#111827',
    titleColor: '#fff',
    bodyColor: '#9ca3af',
    padding: 10,
    cornerRadius: 8,
    displayColors: false,
  };

  // ---- BAR CHART: Patients per day ----
  const barCtx = document.getElementById('chartBar');
  if (barCtx) {
    new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: d.barLabels,
        datasets: [{
          label: 'Appointments',
          data: d.barData,
          backgroundColor: d.barData.map((v, i) =>
            v === Math.max(...d.barData) ? primaryColor : 'rgba(29,158,117,0.18)'
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
          tooltip: { ...tooltipStyle, callbacks: {
            title: (items) => items[0].label,
            label: (item) => ` ${item.raw} appointments`,
          }}
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: textColor, font: baseFont }
          },
          y: {
            beginAtZero: true,
            grid: { color: gridColor },
            ticks: {
              color: textColor,
              font: baseFont,
              stepSize: 1,
              callback: (v) => Number.isInteger(v) ? v : ''
            },
            border: { display: false }
          }
        }
      }
    });
  }

  // ---- LINE CHART: Revenue evolution ----
  const lineCtx = document.getElementById('chartLine');
  if (lineCtx) {
    new Chart(lineCtx, {
      type: 'line',
      data: {
        labels: d.lineLabels.length ? d.lineLabels : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug'],
        datasets: [{
          label: 'Revenue',
          data: d.lineData.length ? d.lineData : [0,0,0,0,0,0,0,0],
          borderColor: primaryColor,
          backgroundColor: 'rgba(29,158,117,0.08)',
          borderWidth: 2.5,
          pointBackgroundColor: primaryColor,
          pointRadius: 4,
          pointHoverRadius: 6,
          tension: 0.4,
          fill: true,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { display: false },
          tooltip: { ...tooltipStyle, callbacks: {
            label: (item) => ` S/ ${Number(item.raw).toLocaleString()}`,
          }}
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: textColor, font: baseFont }
          },
          y: {
            beginAtZero: true,
            grid: { color: gridColor },
            ticks: {
              color: textColor,
              font: baseFont,
              callback: (v) => 'S/ ' + v.toLocaleString()
            },
            border: { display: false }
          }
        }
      }
    });
  }

  // ---- DONUT CHART: Procedures ----
  const donutCtx = document.getElementById('chartDonut');
  if (donutCtx && d.donutData.length > 0) {
    new Chart(donutCtx, {
      type: 'doughnut',
      data: {
        labels: d.donutLabels,
        datasets: [{
          data: d.donutData,
          backgroundColor: d.donutColors.length
            ? d.donutColors
            : ['#1D9E75','#378ADD','#EF9F27','#E24B4A','#7F77DD','#888780'],
          borderWidth: 2,
          borderColor: '#fff',
          hoverOffset: 6,
        }]
      },
      options: {
        responsive: true,
        cutout: '68%',
        plugins: {
          legend: { display: false },
          tooltip: { ...tooltipStyle, callbacks: {
            label: (item) => ` ${item.raw} procedures`,
          }}
        }
      }
    });
  } else if (donutCtx) {
    // placeholder donut when no data
    new Chart(donutCtx, {
      type: 'doughnut',
      data: {
        labels: ['No data'],
        datasets: [{ data: [1], backgroundColor: ['#e5e7eb'], borderWidth: 0 }]
      },
      options: {
        responsive: true,
        cutout: '68%',
        plugins: { legend: { display: false }, tooltip: { enabled: false } }
      }
    });
  }
});
