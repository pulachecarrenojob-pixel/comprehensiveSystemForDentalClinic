<?php $flash = getFlash(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — <?= APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/auth.css') ?>">
</head>
<body class="auth-body">
<div class="auth-container">

  <div class="auth-brand">
    <div class="auth-logo">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
        <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10"/>
        <path d="M8 13s1.5 2 4 2 4-2 4-2"/>
        <line x1="9" y1="9" x2="9.01" y2="9" stroke-width="3"/>
        <line x1="15" y1="9" x2="15.01" y2="9" stroke-width="3"/>
        <circle cx="19" cy="5" r="3" fill="currentColor" stroke="none" opacity="0.7"/>
      </svg>
    </div>
    <h1 class="auth-title"><?= APP_NAME ?></h1>
    <p class="auth-subtitle">Dental Management System</p>
  </div>

  <?php if($flash): ?>
  <div class="alert alert-<?= $flash['type'] === 'error' ? 'error' : 'success' ?> auth-alert">
    <div class="alert-content">
      <span><?= clean($flash['message']) ?></span>
    </div>
  </div>
  <?php endif; ?>

  <div class="auth-card">
    <div class="auth-card-header">
      <h2>Welcome back 👋</h2>
      <p>Sign in to your DentalCare account</p>
    </div>

    <!-- Action must use ?url=login format -->
    <form action="<?= BASE_URL ?>/index.php?url=login" method="POST" class="auth-form">
      <?= csrfField() ?>

      <div class="form-group">
        <label for="email" class="form-label">Email address</label>
        <input type="email" id="email" name="email" class="form-input"
          placeholder="doctor@dentalcare.com"
          value="<?= clean($_POST['email'] ?? '') ?>"
          required autofocus>
      </div>

      <div class="form-group">
        <label for="password" class="form-label">Password</label>
        <div class="input-wrapper">
          <input type="password" id="password" name="password" class="form-input"
            placeholder="••••••••" required>
          <button type="button" class="input-eye" onclick="togglePwd()">
            <svg id="eyeShow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            <svg id="eyeHide" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-primary btn-full">
        Sign in
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </button>
    </form>
  </div>

  <div class="auth-demo-box">
    <div class="auth-demo-title">Demo credentials</div>
    <div class="auth-demo-row">
      <span class="auth-demo-label">Email</span>
      <code class="auth-demo-val" onclick="fillEmail(this)">admin@dentalcare.com</code>
    </div>
    <div class="auth-demo-row">
      <span class="auth-demo-label">Password</span>
      <code class="auth-demo-val" onclick="fillPwd(this)">password</code>
    </div>
  </div>

</div>

<script>
function togglePwd() {
  const i = document.getElementById('password');
  const s = document.getElementById('eyeShow');
  const h = document.getElementById('eyeHide');
  i.type = i.type === 'password' ? 'text' : 'password';
  s.style.display = i.type === 'text' ? 'none' : '';
  h.style.display = i.type === 'text' ? '' : 'none';
}
function fillEmail(el) {
  document.getElementById('email').value = el.textContent;
}
function fillPwd(el) {
  document.getElementById('password').value = el.textContent;
}
</script>
</body>
</html>
