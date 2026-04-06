<?php
$currency = getSettingValue('currency', 'S/');

// Prepare chart data for JS
$barLabels   = json_encode(array_column($patientsPerDay, 'day'));
$barData     = json_encode(array_column($patientsPerDay, 'total'));
$lineLabels  = json_encode(array_column($revenueEvol, 'month'));
$lineData    = json_encode(array_column($revenueEvol, 'total'));
$donutLabels = json_encode(array_column($procedures, 'name'));
$donutData   = json_encode(array_column($procedures, 'total'));
$donutColors = json_encode(array_column($procedures, 'color'));
?>

<!-- KPI Cards -->
<div class="kpi-grid">

  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon green">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
      <div>
        <div class="kpi-label">Today's Appointments</div>
        <div class="kpi-value"><?= $kpis['today_appointments'] ?></div>
        <div class="kpi-delta">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
          Active today
        </div>
      </div>
    </div>
    <div class="kpi-sparkline" id="spark1"></div>
  </div>

  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon blue">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div>
        <div class="kpi-label">Total Patients</div>
        <div class="kpi-value"><?= $kpis['total_patients'] ?></div>
        <div class="kpi-delta">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
          Registered
        </div>
      </div>
    </div>
    <div class="kpi-sparkline" id="spark2"></div>
  </div>

  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon orange">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div>
        <div class="kpi-label">Monthly Revenue</div>
        <div class="kpi-value"><?= $currency ?> <?= number_format($kpis['monthly_revenue'], 0) ?></div>
        <div class="kpi-delta <?= $kpis['revenue_delta'] < 0 ? 'down' : '' ?>">
          <?php if($kpis['revenue_delta'] >= 0): ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            +<?= abs($kpis['revenue_delta']) ?>% vs last month
          <?php else: ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
            <?= $kpis['revenue_delta'] ?>% vs last month
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="kpi-sparkline" id="spark3"></div>
  </div>

  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon green">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div>
        <div class="kpi-label">Confirmed Today</div>
        <div class="kpi-value"><?= $kpis['confirmed_today'] ?></div>
        <div class="kpi-delta">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Ready
        </div>
      </div>
    </div>
    <div class="kpi-sparkline" id="spark4"></div>
  </div>

</div>

<!-- Charts Row -->
<div class="charts-row" style="margin-bottom:20px">

  <!-- Bar chart: patients per day -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Patients by Day of Week</span>
      <span style="font-size:0.75rem;color:var(--text-muted)">Last 30 days</span>
    </div>
    <div class="card-body" style="padding:16px 20px">
      <canvas id="chartBar" height="200"></canvas>
    </div>
  </div>

  <!-- Line chart: revenue evolution -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Revenue Evolution</span>
      <span style="font-size:0.75rem;color:var(--text-muted)">Last 8 months</span>
    </div>
    <div class="card-body" style="padding:16px 20px">
      <canvas id="chartLine" height="200"></canvas>
    </div>
  </div>

</div>

<!-- Bottom Row -->
<div class="grid-2" style="gap:20px">

  <!-- Donut: procedures -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Procedures Breakdown</span>
    </div>
    <div class="card-body">
      <?php if(count($procedures) > 0): ?>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:center">
        <canvas id="chartDonut" style="max-height:200px"></canvas>
        <div>
          <?php foreach($procedures as $p): ?>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
            <div style="display:flex;align-items:center;gap:8px">
              <span style="width:10px;height:10px;border-radius:50%;background:<?= clean($p['color']) ?>;flex-shrink:0;display:inline-block"></span>
              <span style="font-size:0.8rem;color:var(--text-secondary)"><?= clean($p['name']) ?></span>
            </div>
            <span style="font-size:0.8rem;font-weight:600;color:var(--text)"><?= $p['total'] ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
        <h3>No procedures yet</h3>
        <p>Data will appear after recording clinical visits</p>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Today's appointments table -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Today's Schedule</span>
      <a href="<?= url('schedule') ?>" class="btn btn-outline" style="padding:5px 12px;font-size:0.78rem">View all</a>
    </div>
    <?php if(count($todayAppts) > 0): ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Time</th>
            <th>Patient</th>
            <th>Procedure</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($todayAppts as $appt): ?>
          <tr>
            <td style="font-family:var(--font-mono);font-size:0.8rem;color:var(--text-secondary)">
              <?= substr($appt['start_time'], 0, 5) ?>
            </td>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <div style="width:28px;height:28px;border-radius:50%;background:var(--primary-light);color:var(--primary-dark);display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:600;flex-shrink:0">
                  <?= initials($appt['patient_name']) ?>
                </div>
                <span style="font-size:0.85rem;font-weight:500"><?= clean($appt['patient_name']) ?></span>
              </div>
            </td>
            <td>
              <span style="display:inline-flex;align-items:center;gap:5px;font-size:0.8rem">
                <span style="width:8px;height:8px;border-radius:50%;background:<?= clean($appt['procedure_color']) ?>;flex-shrink:0;display:inline-block"></span>
                <?= clean($appt['procedure_name']) ?>
              </span>
            </td>
            <td><?= statusBadge($appt['status']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="card-body">
      <div class="empty-state" style="padding:32px 24px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <h3>No appointments today</h3>
        <p>Schedule a new appointment to get started</p>
      </div>
    </div>
    <?php endif; ?>
  </div>

</div>

<!-- Pass data to JS -->
<script>
  window.dashboardData = {
    barLabels:   <?= $barLabels ?>,
    barData:     <?= $barData ?>,
    lineLabels:  <?= $lineLabels ?>,
    lineData:    <?= $lineData ?>,
    donutLabels: <?= $donutLabels ?>,
    donutData:   <?= $donutData ?>,
    donutColors: <?= $donutColors ?>,
  };
</script>
