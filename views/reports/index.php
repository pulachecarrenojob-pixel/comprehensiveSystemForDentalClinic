<?php $c = $currency; ?>

<!-- Page header -->
<div class="page-header">
  <div class="page-header-left">
    <h2>Reports</h2>
    <p>Analytics and performance overview</p>
  </div>
  <a href="<?= BASE_URL ?>/index.php?url=reports/export&from=<?= $from ?>&to=<?= $to ?>" class="btn btn-outline">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
    Export CSV
  </a>
</div>

<!-- Date filter -->
<div class="card" style="margin-bottom:20px">
  <div class="card-body" style="padding:14px 20px">
    <form method="GET" action="<?= BASE_URL ?>/index.php" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
      <input type="hidden" name="url" value="reports">
      <div style="display:flex;align-items:center;gap:8px">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--text-muted)"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <label style="font-size:0.82rem;font-weight:500;color:var(--text-secondary)">From</label>
        <input type="date" name="from" value="<?= $from ?>" class="form-input" style="width:160px">
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <label style="font-size:0.82rem;font-weight:500;color:var(--text-secondary)">To</label>
        <input type="date" name="to" value="<?= $to ?>" class="form-input" style="width:160px">
      </div>
      <button type="submit" class="btn btn-primary" style="padding:8px 18px">Apply</button>
      <!-- Quick ranges -->
      <div style="display:flex;gap:6px;margin-left:auto;flex-wrap:wrap">
        <?php
        $ranges = [
          'This month'  => [date('Y-m-01'), date('Y-m-t')],
          'Last month'  => [date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last day of last month'))],
          'Last 3 months'=> [date('Y-m-01', strtotime('-2 months')), date('Y-m-t')],
          'This year'   => [date('Y-01-01'), date('Y-12-31')],
        ];
        foreach($ranges as $label => [$rf, $rt]):
        ?>
        <a href="<?= BASE_URL ?>/index.php?url=reports&from=<?= $rf ?>&to=<?= $rt ?>"
           class="btn btn-outline" style="padding:5px 12px;font-size:0.75rem;<?= ($from===$rf && $to===$rt) ? 'background:var(--primary);color:#fff;border-color:var(--primary)' : '' ?>">
          <?= $label ?>
        </a>
        <?php endforeach; ?>
      </div>
    </form>
  </div>
</div>

<!-- Summary KPIs -->
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

<!-- Charts row 1: by dentist -->
<div class="charts-row" style="margin-bottom:20px">
  <div class="card">
    <div class="card-header">
      <span class="card-title">Appointments by Dentist</span>
    </div>
    <div class="card-body" style="padding:16px 20px">
      <canvas id="chartDentistAppts" height="220"></canvas>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <span class="card-title">Revenue by Dentist</span>
    </div>
    <div class="card-body" style="padding:16px 20px">
      <canvas id="chartDentistRev" height="220"></canvas>
    </div>
  </div>
</div>

<!-- Monthly evolution -->
<div class="card" style="margin-bottom:20px">
  <div class="card-header">
    <span class="card-title">Monthly Evolution</span>
    <div style="display:flex;gap:12px">
      <span style="display:flex;align-items:center;gap:5px;font-size:0.75rem;color:var(--text-muted)">
        <span style="width:12px;height:3px;background:#1D9E75;border-radius:2px;display:inline-block"></span> Appointments
      </span>
      <span style="display:flex;align-items:center;gap:5px;font-size:0.75rem;color:var(--text-muted)">
        <span style="width:12px;height:3px;background:#378ADD;border-radius:2px;display:inline-block"></span> Revenue
      </span>
    </div>
  </div>
  <div class="card-body" style="padding:16px 20px">
    <canvas id="chartEvolution" height="160"></canvas>
  </div>
</div>

<!-- Bottom row: procedures + performance -->
<div class="grid-2" style="gap:20px">

  <!-- Top procedures -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Top Procedures</span>
      <span class="badge badge-secondary"><?= count($procedures) ?></span>
    </div>
    <?php if(count($procedures) > 0): ?>
    <div class="card-body">
      <?php
      $maxTotal = max(array_column($procedures, 'total')) ?: 1;
      foreach($procedures as $i => $p):
      ?>
      <div class="proc-row">
        <div style="display:flex;align-items:center;gap:8px;min-width:0;flex:1">
          <span style="width:22px;height:22px;border-radius:6px;background:<?= clean($p['color']) ?>20;color:<?= clean($p['color']) ?>;display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;flex-shrink:0"><?= $i+1 ?></span>
          <div style="min-width:0;flex:1">
            <div style="font-size:0.85rem;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= clean($p['name']) ?></div>
            <div class="proc-bar-wrap">
              <div class="proc-bar" style="width:<?= round(($p['total']/$maxTotal)*100) ?>%;background:<?= clean($p['color']) ?>"></div>
            </div>
          </div>
        </div>
        <div style="text-align:right;flex-shrink:0;margin-left:12px">
          <div style="font-size:0.875rem;font-weight:600"><?= $p['total'] ?></div>
          <div style="font-size:0.72rem;color:var(--text-muted)"><?= $c ?> <?= number_format((float)$p['revenue'],0) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="card-body"><div class="empty-state" style="padding:28px"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg><h3>No data</h3><p>No procedures in this period</p></div></div>
    <?php endif; ?>
  </div>

  <!-- Dentist performance -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Dentist Performance</span>
    </div>
    <?php if(count($performance) > 0): ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Dentist</th>
            <th>Total</th>
            <th>Done</th>
            <th>Rate</th>
            <th>Revenue</th>
          </tr>
        </thead>
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
            <td style="font-size:0.875rem"><?= $p['total'] ?></td>
            <td style="font-size:0.875rem"><?= $p['completed'] ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:6px">
                <div style="flex:1;height:6px;background:var(--gray-200);border-radius:99px;min-width:40px">
                  <div style="height:100%;width:<?= min(100,(int)$p['rate']) ?>%;background:<?= (int)$p['rate']>=70 ? 'var(--primary)' : 'var(--warning)' ?>;border-radius:99px"></div>
                </div>
                <span style="font-size:0.78rem;font-weight:600;white-space:nowrap"><?= $p['rate'] ?>%</span>
              </div>
            </td>
            <td style="font-size:0.875rem;font-weight:600;color:var(--primary)"><?= $c ?> <?= number_format((float)$p['revenue'],0) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="card-body"><div class="empty-state" style="padding:28px"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg><h3>No data</h3><p>No appointments in this period</p></div></div>
    <?php endif; ?>
  </div>

</div>

<script>
window.reportsData = <?= json_encode($chartData) ?>;
window.reportsCurrency = '<?= $c ?>';
</script>
