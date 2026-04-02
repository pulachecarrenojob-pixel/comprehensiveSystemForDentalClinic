<?php $user = Auth::user(); ?>
<aside class="sidebar" id="sidebar">

  <div class="sidebar-brand">
    <div class="brand-icon">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10"/>
        <path d="M12 6v6l4 2"/>
        <circle cx="18" cy="6" r="3" fill="currentColor" stroke="none"/>
      </svg>
    </div>
    <div class="brand-text">
      <span class="brand-name"><?= APP_NAME ?></span>
      <span class="brand-sub">Management System</span>
    </div>
    <button class="sidebar-close" onclick="toggleSidebar()">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
    </button>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section">
      <span class="nav-label">Main</span>
      <a href="<?= url('dashboard') ?>" class="nav-item <?= isActive('/dashboard') ?>">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        <span>Dashboard</span>
      </a>
      <a href="<?= url('schedule') ?>" class="nav-item <?= isActive('/schedule') ?>">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span>Schedule</span>
      </a>
    </div>

    <div class="nav-section">
      <span class="nav-label">Patients</span>
      <a href="<?= url('patients') ?>" class="nav-item <?= isActive('/patients') ?>">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span>Patients</span>
      </a>
      <a href="<?= url('anamnesis') ?>" class="nav-item <?= isActive('/anamnesis') ?>">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        <span>Anamnesis</span>
      </a>
      <a href="<?= url('records') ?>" class="nav-item <?= isActive('/records') ?>">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span>Records</span>
      </a>
    </div>

    <div class="nav-section">
      <span class="nav-label">Finance</span>
      <a href="<?= url('finance') ?>" class="nav-item <?= isActive('/finance') ?>">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <span>Finance</span>
      </a>
      <a href="<?= url('reports') ?>" class="nav-item <?= isActive('/reports') ?>">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        <span>Reports</span>
      </a>
    </div>

    <div class="nav-section">
      <span class="nav-label">System</span>
      <a href="<?= url('settings') ?>" class="nav-item <?= isActive('/settings') ?>">
        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        <span>Settings</span>
      </a>
    </div>
  </nav>

  <div class="sidebar-user">
    <div class="user-avatar"><?= initials($user['name'] ?? 'U') ?></div>
    <div class="user-info">
      <span class="user-name"><?= clean($user['name'] ?? '') ?></span>
      <span class="user-role"><?= ucfirst($user['role'] ?? '') ?></span>
    </div>
    <a href="<?= url('logout') ?>" class="logout-btn" title="Logout">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    </a>
  </div>

</aside>
