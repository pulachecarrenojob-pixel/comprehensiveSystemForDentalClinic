<?php $flash = getFlash(); ?>
<div class="auth-container">

  <div class="auth-brand">
    <div class="auth-logo">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10"/>
        <path d="M8 12s1.5 2 4 2 4-2 4-2"/>
        <line x1="9" y1="9" x2="9.01" y2="9"/>
        <line x1="15" y1="9" x2="15.01" y2="9"/>
        <circle cx="19" cy="5" r="3" fill="currentColor" stroke="none" opacity="0.7"/>
      </svg>
    </div>
    <h1 class="auth-title"><?= APP_NAME ?></h1>
    <p class="auth-subtitle">Dental Management System</p>
  </div>

  <?php if($flash): ?>
  <div class="alert alert-<?= $flash['type'] ?> auth-alert">
    <span><?= clean($flash['message']) ?></span>
  </div>
  <?php endif; ?>

  <div class="auth-card">
    <div class="auth-card-header">
      <h2>Welcome back</h2>
      <p>Sign in to your account</p>
    </div>
    <form action="<?= url('login') ?>" method="POST" class="auth-form">
      <?= csrfField() ?>

      <div class="form-group">
        <label for="email" class="form-label">Email address</label>
        <input
          type="email"
          id="email"
          name="email"
          class="form-input"
          placeholder="doctor@dentalcare.com"
          value="<?= clean($_POST['email'] ?? '') ?>"
          required
          autofocus
        >
      </div>

      <div class="form-group">
        <label for="password" class="form-label">
          Password
        </label>
        <div class="input-wrapper">
          <input
            type="password"
            id="password"
            name="password"
            class="form-input"
            placeholder="••••••••"
            required
          >
          <button type="button" class="input-eye" onclick="togglePassword()">
            <svg id="eyeIcon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-primary btn-full">
        Sign in
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </button>
    </form>
  </div>

  <p class="auth-hint">
    Demo: <code>admin@dentalcare.com</code> / <code>password</code>
  </p>

</div>

<script>
function togglePassword() {
  const input = document.getElementById('password');
  input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
