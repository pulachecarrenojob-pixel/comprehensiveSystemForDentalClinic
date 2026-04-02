<?php $user = Auth::user(); ?>
<header class="topbar">
  <div class="topbar-left">
    <button class="menu-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <div class="page-info">
      <h1 class="page-title"><?= $title ?? 'Dashboard' ?></h1>
      <span class="page-date"><?= date('l, d F Y') ?></span>
    </div>
  </div>
  <div class="topbar-right">
    <div class="topbar-user">
      <div class="user-avatar-sm"><?= initials($user['name'] ?? 'U') ?></div>
      <span class="user-name-sm"><?= clean($user['name'] ?? '') ?></span>
    </div>
  </div>
</header>
