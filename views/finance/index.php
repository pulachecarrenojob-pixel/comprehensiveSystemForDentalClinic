<?php
// All data comes pre-processed from FinanceController — no model calls here
$c = $kpis['currency'] ?? 'S/';
?>

<!-- Page header -->
<div class="page-header">
  <div class="page-header-left">
    <h2>Finance</h2>
    <p>Revenue tracking and payment management</p>
  </div>
  <button class="btn btn-primary" onclick="openModal('paymentModal')">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Register Payment
  </button>
</div>

<!-- KPI Cards -->
<div class="kpi-grid" style="margin-bottom:24px">

  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon green">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div>
        <div class="kpi-label">Monthly Revenue</div>
        <div class="kpi-value"><?= $c ?> <?= number_format($kpis['month_revenue'], 0) ?></div>
        <div class="kpi-delta <?= $kpis['revenue_delta'] < 0 ? 'down' : '' ?>">
          <?= $kpis['revenue_delta'] >= 0 ? '▲' : '▼' ?> <?= abs($kpis['revenue_delta']) ?>% vs last month
        </div>
      </div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon orange">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </div>
      <div>
        <div class="kpi-label">Pending</div>
        <div class="kpi-value"><?= $c ?> <?= number_format($kpis['pending_amount'], 0) ?></div>
        <div class="kpi-delta" style="color:var(--text-muted)"><?= $kpis['pending_count'] ?> transactions</div>
      </div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon blue">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg>
      </div>
      <div>
        <div class="kpi-label">Avg Ticket</div>
        <div class="kpi-value"><?= $c ?> <?= number_format($kpis['avg_ticket'], 0) ?></div>
        <div class="kpi-delta" style="color:var(--text-muted)">Per appointment</div>
      </div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-left">
      <div class="kpi-icon green">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
      </div>
      <div>
        <div class="kpi-label">Transactions</div>
        <div class="kpi-value"><?= $kpis['total_tx'] ?></div>
        <div class="kpi-delta" style="color:var(--text-muted)">This month</div>
      </div>
    </div>
  </div>

</div>

<!-- Charts row -->
<div class="charts-row" style="margin-bottom:20px">

  <!-- Daily revenue bar chart -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Daily Revenue</span>
      <span style="font-size:0.75rem;color:var(--text-muted)">Last 14 days</span>
    </div>
    <div class="card-body" style="padding:16px 20px">
      <canvas id="chartDaily" height="200"></canvas>
    </div>
  </div>

  <!-- Payment methods donut -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Payment Methods</span>
      <span style="font-size:0.75rem;color:var(--text-muted)">This month</span>
    </div>
    <div class="card-body">
      <?php if(count($methodBreakFormatted) > 0): ?>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:center">
        <canvas id="chartMethods" style="max-height:180px"></canvas>
        <div>
          <?php foreach($methodBreakFormatted as $mb): ?>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
            <div style="display:flex;align-items:center;gap:7px">
              <span style="width:10px;height:10px;border-radius:50%;background:<?= clean($mb['color']) ?>;flex-shrink:0;display:inline-block"></span>
              <span style="font-size:0.8rem;color:var(--text-secondary)"><?= clean($mb['label']) ?></span>
            </div>
            <span style="font-size:0.8rem;font-weight:600"><?= $c ?> <?= number_format($mb['total'], 0) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php else: ?>
      <div class="empty-state" style="padding:32px">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        <h3>No data yet</h3>
        <p>Register payments to see the breakdown</p>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- Transactions table -->
<div class="card">
  <div class="card-header" style="flex-wrap:wrap;gap:10px">
    <span class="card-title">Transactions</span>
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-left:auto">
      <div class="search-bar" style="max-width:220px">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="txSearch" class="search-input" placeholder="Search patient..." value="<?= clean($search) ?>">
      </div>
      <select id="statusFilter" class="form-select" style="width:auto" onchange="applyFilters()">
        <option value="" <?= !$statusFilter ? 'selected':'' ?>>All status</option>
        <option value="paid"     <?= $statusFilter==='paid'     ? 'selected':'' ?>>Paid</option>
        <option value="pending"  <?= $statusFilter==='pending'  ? 'selected':'' ?>>Pending</option>
        <option value="refunded" <?= $statusFilter==='refunded' ? 'selected':'' ?>>Refunded</option>
      </select>
      <select id="methodFilter" class="form-select" style="width:auto" onchange="applyFilters()">
        <option value="" <?= !$methodFilter ? 'selected':'' ?>>All methods</option>
        <option value="cash"        <?= $methodFilter==='cash'        ? 'selected':'' ?>>Cash</option>
        <option value="credit_card" <?= $methodFilter==='credit_card' ? 'selected':'' ?>>Credit Card</option>
        <option value="debit_card"  <?= $methodFilter==='debit_card'  ? 'selected':'' ?>>Debit Card</option>
        <option value="transfer"    <?= $methodFilter==='transfer'    ? 'selected':'' ?>>Transfer</option>
        <option value="insurance"   <?= $methodFilter==='insurance'   ? 'selected':'' ?>>Insurance</option>
      </select>
    </div>
  </div>

  <?php if(count($transactions) > 0): ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Patient</th>
          <th>Procedure</th>
          <th>Method</th>
          <th>Amount</th>
          <th>Date</th>
          <th>Status</th>
          <th style="text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody id="txBody">
        <?php foreach($transactions as $t): ?>
        <tr class="tx-row" data-search="<?= clean(strtolower($t['patient_name'] ?? '')) ?>">
          <td>
            <div style="display:flex;align-items:center;gap:9px">
              <div class="patient-avatar" style="background:var(--primary-light);color:var(--primary-dark)">
                <?= initials($t['patient_name'] ?? 'U') ?>
              </div>
              <span style="font-size:0.875rem;font-weight:500"><?= clean($t['patient_name'] ?? '—') ?></span>
            </div>
          </td>
          <td style="font-size:0.85rem;color:var(--text-secondary)"><?= clean($t['procedure_name'] ?? '—') ?></td>
          <td>
            <span class="method-badge" style="background:<?= clean($t['method_color']) ?>18;color:<?= clean($t['method_color']) ?>;border:1px solid <?= clean($t['method_color']) ?>30">
              <?= clean($t['method_label']) ?>
            </span>
          </td>
          <td style="font-size:0.95rem;font-weight:600;color:var(--text)">
            <?= $c ?> <?= number_format((float)$t['amount'], 2) ?>
          </td>
          <td style="font-size:0.82rem;color:var(--text-secondary);white-space:nowrap">
            <?= !empty($t['paid_at']) ? formatDate($t['paid_at']) : formatDate($t['created_at'] ?? '') ?>
          </td>
          <td><?= statusBadge($t['status'] ?? 'pending') ?></td>
          <td>
            <div style="display:flex;justify-content:flex-end">
              <?php if($t['status'] === 'pending'): ?>
              <form method="POST" action="<?= url('finance/update') ?>">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <input type="hidden" name="status" value="paid">
                <button type="submit" class="btn btn-primary" style="padding:4px 12px;font-size:0.78rem">Mark Paid</button>
              </form>
              <?php else: ?>
              <span style="font-size:0.75rem;color:var(--text-muted)">—</span>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="card-body">
    <div class="empty-state">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
      <h3>No transactions yet</h3>
      <p>Register a payment to get started</p>
      <button onclick="openModal('paymentModal')" class="btn btn-primary" style="margin-top:14px">Register Payment</button>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- ============================================================
     REGISTER PAYMENT MODAL
     ============================================================ -->
<div class="modal-backdrop" id="paymentModal">
  <div class="modal" style="max-width:520px">
    <div class="modal-header">
      <span class="modal-title">Register Payment</span>
      <button class="modal-close" onclick="closeModal('paymentModal')">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <form method="POST" action="<?= url('finance/store') ?>">
      <?= csrfField() ?>
      <div class="modal-body">

        <div class="form-group">
          <label class="form-label">Appointment <span style="color:var(--danger)">*</span></label>
          <?php if(count($pending) > 0): ?>
          <select name="appointment_id" id="apptSelector" class="form-select" required onchange="fillPaymentForm(this)">
            <option value="">Select appointment...</option>
            <?php foreach($pending as $p): ?>
            <option value="<?= (int)$p['appointment_id'] ?>"
              data-patient="<?= (int)$p['patient_id'] ?>"
              data-price="<?= number_format((float)$p['price'], 2, '.', '') ?>"
              data-name="<?= clean($p['patient_name']) ?>"
              data-proc="<?= clean($p['procedure_name']) ?>">
              <?= formatDate($p['date']) ?> — <?= clean($p['patient_name']) ?> — <?= clean($p['procedure_name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
          <?php else: ?>
          <div style="padding:10px 12px;background:var(--gray-50);border-radius:8px;font-size:0.85rem;color:var(--text-muted);border:1px solid var(--border)">
            No pending appointments without payment
          </div>
          <input type="hidden" name="appointment_id" value="0">
          <?php endif; ?>
        </div>

        <!-- Auto-filled appointment info -->
        <div id="apptInfo" style="display:none;margin-bottom:16px;padding:12px 14px;background:var(--primary-light);border-radius:8px;border:1px solid #b7ebd9">
          <div style="font-weight:600;color:var(--primary-dark);font-size:0.875rem" id="apptPatientName"></div>
          <div style="color:var(--text-secondary);margin-top:2px;font-size:0.8rem" id="apptProcName"></div>
        </div>
        <input type="hidden" name="patient_id" id="patientIdHidden" value="0">

        <div class="form-row">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Amount <span style="color:var(--danger)">*</span></label>
            <div style="position:relative">
              <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.85rem;pointer-events:none"><?= $c ?></span>
              <input type="number" name="amount" id="amountInput" class="form-input"
                step="0.01" min="0.01" required placeholder="0.00"
                style="padding-left:30px">
            </div>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Payment Method <span style="color:var(--danger)">*</span></label>
            <select name="payment_method" class="form-select" required>
              <option value="cash">Cash</option>
              <option value="credit_card">Credit Card</option>
              <option value="debit_card">Debit Card</option>
              <option value="transfer">Transfer</option>
              <option value="insurance">Insurance</option>
              <option value="other">Other</option>
            </select>
          </div>
        </div>

        <div class="form-group" style="margin-top:16px;margin-bottom:0">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-textarea" rows="2" placeholder="Optional payment notes..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('paymentModal')">Cancel</button>
        <button type="submit" class="btn btn-primary">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Confirm Payment
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Chart data passed from controller -->
<script>
window.financeData = {
  dailyLabels:  <?= $chartData['dailyLabels'] ?>,
  dailyData:    <?= $chartData['dailyData'] ?>,
  methodLabels: <?= $chartData['methodLabels'] ?>,
  methodData:   <?= $chartData['methodData'] ?>,
  methodColors: <?= $chartData['methodColors'] ?>,
};
</script>
