<?php $flash = getFlash(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — <?= APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #1D9E75;
      --primary-dark: #157a5a;
      --primary-glow: rgba(29,158,117,0.35);
      --font: 'DM Sans', system-ui, sans-serif;
      --mono: 'DM Mono', monospace;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; font-family: var(--font); -webkit-font-smoothing: antialiased; }

    body {
      display: grid;
      grid-template-columns: 1fr 1fr;
      min-height: 100vh;
      background: #0a0f0d;
    }

    /* ---- LEFT PANEL ---- */
    .left-panel {
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 48px;
      background: #0d1a14;
      overflow: hidden;
    }
    .left-panel::before {
      content: '';
      position: absolute;
      top: -120px; left: -120px;
      width: 500px; height: 500px;
      background: radial-gradient(circle, rgba(29,158,117,0.18) 0%, transparent 70%);
      pointer-events: none;
    }
    .left-panel::after {
      content: '';
      position: absolute;
      bottom: -80px; right: -80px;
      width: 350px; height: 350px;
      background: radial-gradient(circle, rgba(55,138,221,0.12) 0%, transparent 70%);
      pointer-events: none;
    }
    .grid-bg {
      position: absolute; inset: 0;
      background-image:
        linear-gradient(rgba(29,158,117,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(29,158,117,0.04) 1px, transparent 1px);
      background-size: 40px 40px;
    }
    .left-brand { position: relative; z-index: 1; }
    .brand-logo {
      width: 48px; height: 48px;
      background: var(--primary);
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 16px;
      box-shadow: 0 0 32px var(--primary-glow);
    }
    .brand-logo svg { width: 26px; height: 26px; color: #fff; }
    .brand-name    { font-size: 1.5rem; font-weight: 600; color: #fff; }
    .brand-tagline { font-size: 0.82rem; color: rgba(255,255,255,0.35); margin-top: 4px; }

    .left-hero {
      position: relative; z-index: 1;
      flex: 1; display: flex; flex-direction: column; justify-content: center;
      padding: 40px 0;
    }
    .hero-label {
      display: inline-block;
      font-size: 0.7rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase;
      color: var(--primary);
      background: rgba(29,158,117,0.12);
      border: 1px solid rgba(29,158,117,0.2);
      padding: 4px 12px; border-radius: 99px; margin-bottom: 20px;
    }
    .hero-title {
      font-size: 2.4rem; font-weight: 600; color: #fff; line-height: 1.2; margin-bottom: 16px;
    }
    .hero-title span { color: var(--primary); }
    .hero-desc { font-size: 0.9rem; color: rgba(255,255,255,0.4); line-height: 1.7; max-width: 380px; }

    .stats-row {
      position: relative; z-index: 1;
      display: grid; grid-template-columns: repeat(3,1fr); gap: 16px;
    }
    .stat-item {
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(255,255,255,0.07);
      border-radius: 12px; padding: 16px;
    }
    .stat-value { font-size: 1.4rem; font-weight: 600; color: #fff; }
    .stat-label { font-size: 0.72rem; color: rgba(255,255,255,0.35); margin-top: 3px; }

    /* ---- RIGHT PANEL ---- */
    .right-panel {
      display: flex; align-items: center; justify-content: center;
      padding: 48px 56px;
      background: #fff;
    }
    .login-box { width: 100%; max-width: 380px; }

    .login-header { margin-bottom: 28px; }
    .login-header h2 { font-size: 1.6rem; font-weight: 600; color: #0a0f0d; }
    .login-header p  { font-size: 0.875rem; color: #6b7280; margin-top: 6px; }

    .flash-alert {
      display: flex; align-items: center; gap: 10px;
      padding: 12px 14px; border-radius: 8px;
      font-size: 0.85rem; margin-bottom: 20px;
    }
    .flash-error   { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
    .flash-success { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }

    .form-group { margin-bottom: 18px; }
    .form-label  { display:block; font-size:0.82rem; font-weight:500; color:#374151; margin-bottom:7px; }
    .form-input  {
      width:100%; padding:11px 14px;
      border:1.5px solid #e5e7eb; border-radius:10px;
      font-size:0.9rem; font-family:var(--font); color:#111827;
      background:#fafafa; outline:none;
      transition:all 0.18s ease;
    }
    .form-input:focus  { border-color:var(--primary); background:#fff; box-shadow:0 0 0 3px rgba(29,158,117,0.1); }
    .form-input::placeholder { color:#9ca3af; }
    .input-wrap { position:relative; }
    .input-wrap .form-input { padding-right:42px; }
    .eye-btn {
      position:absolute; right:12px; top:50%; transform:translateY(-50%);
      color:#9ca3af; cursor:pointer; padding:4px;
      background:none; border:none; transition:color 0.15s;
    }
    .eye-btn:hover { color:#374151; }

    .btn-signin {
      width:100%; padding:12px;
      background:var(--primary); color:#fff;
      border:none; border-radius:10px;
      font-size:0.95rem; font-weight:500; font-family:var(--font);
      cursor:pointer;
      display:flex; align-items:center; justify-content:center; gap:8px;
      transition:all 0.18s ease; margin-top:6px;
      box-shadow:0 4px 16px rgba(29,158,117,0.25);
    }
    .btn-signin:hover { background:var(--primary-dark); box-shadow:0 6px 20px rgba(29,158,117,0.35); transform:translateY(-1px); }
    .btn-signin:active { transform:translateY(0); }
    .btn-signin svg { width:16px; height:16px; }

    .divider {
      display:flex; align-items:center; gap:12px;
      margin:24px 0; color:#d1d5db; font-size:0.78rem;
    }
    .divider::before, .divider::after { content:''; flex:1; height:1px; background:#e5e7eb; }

    .demo-box {
      background:#f9fafb; border:1px solid #e5e7eb;
      border-radius:10px; padding:14px 16px;
    }
    .demo-title { font-size:0.72rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:10px; }
    .demo-row   { display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; }
    .demo-row:last-child { margin-bottom:0; }
    .demo-label { font-size:0.78rem; color:#9ca3af; }
    .demo-val {
      font-family:var(--mono); font-size:0.78rem; color:#374151;
      background:#fff; border:1px solid #e5e7eb;
      padding:2px 8px; border-radius:5px;
      cursor:pointer; transition:all 0.15s;
    }
    .demo-val:hover { background:#f0fdf4; border-color:var(--primary); color:var(--primary); }

    @media (max-width: 900px) {
      body { grid-template-columns:1fr; }
      .left-panel { display:none; }
      .right-panel { padding:40px 24px; background:#f4f6f8; }
      .login-box { background:#fff; padding:32px; border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
    }
  </style>
</head>
<body>

  <!-- LEFT PANEL -->
  <div class="left-panel">
    <div class="grid-bg"></div>
    <div class="left-brand">
      <div class="brand-logo">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
          <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10"/>
          <path d="M8 13s1.5 2 4 2 4-2 4-2"/>
          <line x1="9" y1="9" x2="9.01" y2="9" stroke-width="3"/>
          <line x1="15" y1="9" x2="15.01" y2="9" stroke-width="3"/>
          <circle cx="19" cy="5" r="3" fill="currentColor" stroke="none" opacity="0.8"/>
        </svg>
      </div>
      <div class="brand-name"><?= APP_NAME ?></div>
      <div class="brand-tagline">Dental Management System</div>
    </div>

    <div class="left-hero">
      <div class="hero-label">Trusted by dental professionals</div>
      <h1 class="hero-title">Modern care,<br><span>smarter</span> management.</h1>
      <p class="hero-desc">Complete dental clinic management — patients, appointments, records, finance and reports in one elegant platform.</p>
    </div>

    <div class="stats-row">
      <div class="stat-item"><div class="stat-value">500+</div><div class="stat-label">Patients managed</div></div>
      <div class="stat-item"><div class="stat-value">8</div><div class="stat-label">Modules available</div></div>
      <div class="stat-item"><div class="stat-value">99%</div><div class="stat-label">Uptime reliability</div></div>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="right-panel">
    <div class="login-box">
      <div class="login-header">
        <h2>Welcome back 👋</h2>
        <p>Sign in to your DentalCare account</p>
      </div>

      <?php if($flash): ?>
      <div class="flash-alert flash-<?= $flash['type']==='error' ? 'error' : 'success' ?>">
        <?= clean($flash['message']) ?>
      </div>
      <?php endif; ?>

      <form action="<?= BASE_URL ?>/index.php?url=login" method="POST">
        <?= csrfField() ?>
        <div class="form-group">
          <label class="form-label" for="email">Email address</label>
          <input type="email" id="email" name="email" class="form-input"
            placeholder="doctor@dentalcare.com"
            value="<?= clean($_POST['email'] ?? '') ?>"
            required autofocus>
        </div>
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-wrap">
            <input type="password" id="password" name="password" class="form-input"
              placeholder="••••••••" required>
            <button type="button" class="eye-btn" onclick="togglePwd()">
              <svg id="eyeShow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg id="eyeHide" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>
        <button type="submit" class="btn-signin">
          Sign in
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
      </form>

      <div class="divider">demo credentials</div>

      <div class="demo-box">
        <div class="demo-title">Test account</div>
        <div class="demo-row">
          <span class="demo-label">Email</span>
          <span class="demo-val" onclick="fillEmail(this)">admin@dentalcare.com</span>
        </div>
        <div class="demo-row">
          <span class="demo-label">Password</span>
          <span class="demo-val" onclick="fillPwd(this)">password</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    function togglePwd() {
      const i=document.getElementById('password'),s=document.getElementById('eyeShow'),h=document.getElementById('eyeHide');
      i.type=i.type==='password'?'text':'password';
      s.style.display=i.type==='text'?'none':''; h.style.display=i.type==='text'?'':'none';
    }
    function fillEmail(el){ document.getElementById('email').value=el.textContent; }
    function fillPwd(el)  { document.getElementById('password').value=el.textContent; }
  </script>
</body>
</html>
