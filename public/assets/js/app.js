/* ============================================================
   DentalCare — app.js
   Global JavaScript: sidebar, CSRF, utilities
   ============================================================ */

// Sidebar toggle
function toggleSidebar() {
  const sidebar  = document.getElementById('sidebar');
  const overlay  = document.getElementById('sidebarOverlay');
  if (!sidebar) return;
  sidebar.classList.toggle('open');
  overlay.classList.toggle('open');
  document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
}

// Close sidebar on ESC
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    const sidebar = document.getElementById('sidebar');
    if (sidebar && sidebar.classList.contains('open')) toggleSidebar();
  }
});

// Modal helpers
function openModal(id) {
  const m = document.getElementById(id);
  if (m) { m.classList.add('open'); document.body.style.overflow = 'hidden'; }
}
function closeModal(id) {
  const m = document.getElementById(id);
  if (m) { m.classList.remove('open'); document.body.style.overflow = ''; }
}

// Close modal on backdrop click
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-backdrop')) {
    e.target.classList.remove('open');
    document.body.style.overflow = '';
  }
});

// AJAX fetch wrapper with CSRF
async function apiFetch(url, options = {}) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const defaults = {
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'X-Requested-With': 'XMLHttpRequest',
    }
  };
  const response = await fetch(url, { ...defaults, ...options });
  if (!response.ok) throw new Error('Network error: ' + response.status);
  return response.json();
}

// Format currency
function formatMoney(amount) {
  return new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(amount);
}

// Format date
function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
}

// Show toast notification
function showToast(message, type = 'success') {
  const existing = document.querySelector('.toast');
  if (existing) existing.remove();
  const toast = document.createElement('div');
  toast.className = 'toast toast-' + type;
  toast.textContent = message;
  toast.style.cssText = `
    position:fixed; bottom:24px; right:24px; z-index:9999;
    background:${type==='success'?'#1D9E75':type==='error'?'#E24B4A':'#EF9F27'};
    color:#fff; padding:12px 20px; border-radius:8px;
    font-size:0.875rem; font-weight:500;
    box-shadow:0 4px 16px rgba(0,0,0,0.15);
    animation:slideIn 0.25s ease;
  `;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 3500);
}

// Init on DOM ready
document.addEventListener('DOMContentLoaded', () => {
  // Active nav item highlight
  const path = window.location.pathname;
  document.querySelectorAll('.nav-item').forEach(item => {
    const href = item.getAttribute('href') || '';
    if (href !== '/' && path.startsWith(new URL(href, window.location.origin).pathname)) {
      item.classList.add('active');
    }
  });
});
