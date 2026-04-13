<?php
$c       = $currency;
$baseUrl = BASE_URL . '/index.php';

$ranges = [
    'This month'    => [date('Y-m-01'),                                       date('Y-m-t')],
    'Last month'    => [date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last day of last month'))],
    'Last 3 months' => [date('Y-m-01', strtotime('-2 months')),               date('Y-m-t')],
    'This year'     => [date('Y-01-01'),                                      date('Y-12-31')],
];
?>

<div class="page-header">
  <div class="page-header-left">
    <h2>Reports</h2>
    <p>Analytics for <?= formatDate($from) ?> — <?= formatDate($to) ?></p>
  </div>
  <a href="<?= $baseUrl ?>?url=reports/export&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>"
     class="btn btn-outline" target="_blank">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
    Export CSV
  </a>
</div>

<!-- Date filter -->
<div class="card" style="margin-bottom:20px">
  <div class="card-body" style="padding:14px 20px">
    <form method="GET" action="<?= $baseUrl ?>" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
      <input type="hidden" name="url" value="reports">
      <div style="display:flex;align-items:center;gap:8px">
        <label style="font-size:0.82rem;font-weight:500;color:var(--text-secondary);white-space:nowrap">From</label>
        <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="form-input" style="width:155px">
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <label style="font-size:0.82rem;font-weight:500;color:var(--text-secondary);white-space:nowrap">To</label>
        <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="form-input" style="width:155px">
      </div>
      <button type="submit" class="btn btn-primary" style="padding:8px 20px">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Apply
      </button>
      <div style="display:flex;gap:6px;flex-wrap:wrap;margin-left:auto">
        <?php foreach($ranges as $label => [$rf, $rt]):
          $active = ($from === $rf && $to === $rt);
        ?>
        <a href="<?= $baseUrl ?>?url=reports&from=<?= urlencode($rf) ?>&to=<?= urlencode($rt) ?>"
           class="btn <?= $active ? 'btn-primary' : 'btn-outline' ?>"
           style="padding:5px 12px;font-size:0.75rem">
          <?= $label ?>
        </a>
        <?php endforeach; ?>
      </div>
    </form>
  </div>
</div>

<!-- KPIs -->
<div class="kpi-grid" style="margin-bottom:24px">
  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon blue">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      </div>
      <div>
        <div class="kpi-label">Appointments</div>
        <div class="kpi-value"><?= $kpis['total_appts'] ?></div>
        <div class="kpi-delta" style="color:var(--text-muted)">In period</div>
      </div>
    </div>
  </div>
  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon green">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <div>
        <div class="kpi-label">Completed</div>
        <div class="kpi-value"><?= $kpis['attended'] ?></div>
        <div class="kpi-delta" style="color:var(--text-muted)">Attended</div>
      </div>
    </div>
  </div>
  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon green">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div>
        <div class="kpi-label">Revenue</div>
        <div class="kpi-value"><?= $c ?> <?= number_format($kpis['revenue'], 0) ?></div>
        <div class="kpi-delta" style="color:var(--text-muted)">Total collected</div>
      </div>
    </div>
  </div>
  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon orange">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      </div>
      <div>
        <div class="kpi-label">Attendance Rate</div>
        <div class="kpi-value"><?= $kpis['attend_rate'] ?>%</div>
        <div class="kpi-delta" style="color:var(--text-muted)">Completion rate</div>
      </div>
    </div>
  </div>
</div>

<!-- Charts row -->
<div class="charts-row" style="margin-bottom:20px">
  <div class="card">
    <div class="card-header"><span class="card-title">Appointments by Dentist</span></div>
    <div class="card-body" style="padding:16px 20px">
      <?php if(count($byDentist) > 0): ?>
      <canvas id="chartDentistAppts" height="220"></canvas>
      <?php else: ?>
      <div class="empty-state" style="padding:28px"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/></svg><h3>No data</h3><p>No appointments in this period</p></div>
      <?php endif; ?>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">Revenue by Dentist</span></div>
    <div class="card-body" style="padding:16px 20px">
      <?php if(count($revByDentist) > 0): ?>
      <canvas id="chartDentistRev" height="220"></canvas>
      <?php else: ?>
      <div class="empty-state" style="padding:28px"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg><h3>No data</h3><p>No revenue in this period</p></div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Evolution line chart -->
<div class="card" style="margin-bottom:20px">
  <div class="card-header">
    <span class="card-title">Monthly Evolution</span>
    <div style="display:flex;gap:14px">
      <span style="display:flex;align-items:center;gap:5px;font-size:0.75rem;color:var(--text-muted)">
        <span style="width:14px;height:3px;background:#1D9E75;border-radius:2px;display:inline-block"></span>Appointments
      </span>
      <span style="display:flex;align-items:center;gap:5px;font-size:0.75rem;color:var(--text-muted)">
        <span style="width:14px;height:3px;background:#378ADD;border-radius:2px;display:inline-block"></span>Revenue
      </span>
    </div>
  </div>
  <div class="card-body" style="padding:16px 20px">
    <?php if(count($evolution) > 0): ?>
    <canvas id="chartEvolution" height="160"></canvas>
    <?php else: ?>
    <div class="empty-state" style="padding:28px"><h3>No data for this period</h3></div>
    <?php endif; ?>
  </div>
</div>

<!-- Procedures + Performance -->
<div class="grid-2" style="gap:20px;margin-bottom:20px">
  <div class="card">
    <div class="card-header">
      <span class="card-title">Top Procedures</span>
      <span class="badge badge-secondary"><?= count($procedures) ?></span>
    </div>
    <?php if(count($procedures) > 0):
      $maxTotal = max(array_column($procedures, 'total')) ?: 1;
    ?>
    <div class="card-body">
      <?php foreach($procedures as $i => $p): ?>
      <div class="proc-row">
        <div style="display:flex;align-items:center;gap:8px;flex:1;min-width:0">
          <span style="width:22px;height:22px;border-radius:6px;background:<?= clean($p['color']) ?>20;color:<?= clean($p['color']) ?>;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;flex-shrink:0"><?= $i+1 ?></span>
          <div style="flex:1;min-width:0">
            <div style="font-size:0.85rem;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= clean($p['name']) ?></div>
            <div class="proc-bar-wrap">
              <div class="proc-bar" style="width:<?= round(($p['total']/$maxTotal)*100) ?>%;background:<?= clean($p['color']) ?>"></div>
            </div>
          </div>
        </div>
        <div style="text-align:right;flex-shrink:0;margin-left:12px">
          <div style="font-size:0.875rem;font-weight:600"><?= (int)$p['total'] ?></div>
          <div style="font-size:0.72rem;color:var(--text-muted)"><?= $c ?> <?= number_format((float)$p['revenue'],0) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="card-body"><div class="empty-state" style="padding:28px"><h3>No procedures</h3><p>No data in this period</p></div></div>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">Dentist Performance</span></div>
    <?php if(count($performance) > 0): ?>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Dentist</th><th>Total</th><th>Done</th><th>Rate</th><th>Revenue</th></tr></thead>
        <tbody>
          <?php foreach($performance as $p): ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <span style="width:10px;height:10px;border-radius:50%;background:<?= clean($p['color']) ?>;flex-shrink:0;display:inline-block"></span>
                <div>
                  <div style="font-size:0.85rem;font-weight:500"><?= clean($p['dentist_name']) ?></div>
                  <div style="font-size:0.7rem;color:var(--text-muted)"><?= clean($p['specialty']) ?></div>
                </div>
              </div>
            </td>
            <td style="font-size:0.875rem"><?= (int)$p['total'] ?></td>
            <td style="font-size:0.875rem"><?= (int)$p['completed'] ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:6px">
                <div style="flex:1;height:6px;background:var(--gray-200);border-radius:99px;min-width:40px">
                  <div style="height:100%;width:<?= min(100,(int)($p['rate']??0)) ?>%;background:<?= (int)($p['rate']??0)>=70?'var(--primary)':'var(--warning)' ?>;border-radius:99px"></div>
                </div>
                <span style="font-size:0.78rem;font-weight:600;white-space:nowrap"><?= (int)($p['rate']??0) ?>%</span>
              </div>
            </td>
            <td style="font-size:0.875rem;font-weight:600;color:var(--primary)"><?= $c ?> <?= number_format((float)$p['revenue'],0) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="card-body"><div class="empty-state" style="padding:28px"><h3>No performance data</h3></div></div>
    <?php endif; ?>
  </div>
</div>

<script>
window.reportsData     = <?= json_encode($chartData) ?>;
window.reportsCurrency = '<?= $c ?>';
</script>
