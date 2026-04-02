<div class="page-header">
  <div class="page-header-left">
    <h2>Dashboard</h2>
    <p>Welcome back, <?= clean(Auth::user()['name'] ?? '') ?></p>
  </div>
</div>

<div class="kpi-grid">
  <div class="kpi-card">
    <div>
      <div class="kpi-label">Today's Appointments</div>
      <div class="kpi-value">0</div>
      <div class="kpi-delta">System ready</div>
    </div>
    <div class="kpi-icon green">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </div>
  </div>
  <div class="kpi-card">
    <div>
      <div class="kpi-label">Total Patients</div>
      <div class="kpi-value">5</div>
      <div class="kpi-delta">Sample data loaded</div>
    </div>
    <div class="kpi-icon blue">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
    </div>
  </div>
  <div class="kpi-card">
    <div>
      <div class="kpi-label">Monthly Revenue</div>
      <div class="kpi-value">S/ 0</div>
      <div class="kpi-delta">Ready to track</div>
    </div>
    <div class="kpi-icon orange">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    </div>
  </div>
  <div class="kpi-card">
    <div>
      <div class="kpi-label">Confirmed</div>
      <div class="kpi-value">3</div>
      <div class="kpi-delta">Today</div>
    </div>
    <div class="kpi-icon green">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
  </div>
</div>

<div class="card" style="padding:32px;text-align:center">
  <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#1D9E75" stroke-width="1.5" style="margin:0 auto 12px"><polyline points="20 6 9 17 4 12"/></svg>
  <h3 style="font-size:1.1rem;font-weight:600;margin-bottom:6px">DentalCare is running!</h3>
  <p style="color:var(--text-secondary);font-size:0.875rem">Core system, database and layout are working correctly.<br>Next step: build the remaining modules.</p>
</div>
