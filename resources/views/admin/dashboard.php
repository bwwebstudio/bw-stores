<?php

use App\Core\View;
View::layout('layouts.admin');

View::section('title'); ?>Executive Overview — Super Admin Portal<?php View::endSection();
View::section('page_title'); ?>System Overview & Platform Analytics<?php View::endSection();

View::section('content'); ?>

<!-- Pending UPI Payments Verification Alert Banner -->
<?php if (!empty($stats['pending_payments'])): ?>
<div class="alert alert-warning border border-warning shadow-sm mb-4" style="border-radius: 14px; background-color: #FFFBEB;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-warning text-dark p-2 fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <h6 class="fw-800 text-dark mb-0">⚡ <?= (int)$stats['pending_payments'] ?> Subscription Payment(s) Awaiting Bank UTR Verification!</h6>
                <div class="text-secondary small">Merchants have submitted bank transfer/UPI UTRs. Verify and approve to instantly activate their stores.</div>
            </div>
        </div>
        <a href="<?= url('admin/payments') ?>" class="btn btn-warning btn-sm px-4 fw-800 shadow-sm" style="border-radius: 8px;">
            Review Payments &rarr;
        </a>
    </div>
</div>
<?php endif; ?>

<!-- 1. Executive Platform KPI Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card-widget stat-primary">
            <div class="stat-info">
                <div class="stat-label">Active MRR</div>
                <div class="stat-value font-mono">₹<?= number_format((float)($stats['mrr'] ?? 0), 2) ?></div>
                <div class="stat-subtext">Monthly recurring revenue</div>
            </div>
            <div class="stat-icon-wrapper stat-icon-blue">
                <i class="bi bi-currency-rupee"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card-widget stat-emerald">
            <div class="stat-info">
                <div class="stat-label">Total Platform Revenue</div>
                <div class="stat-value font-mono text-success">₹<?= number_format((float)($stats['total_revenue'] ?? 0), 2) ?></div>
                <div class="stat-subtext">All-time collected</div>
            </div>
            <div class="stat-icon-wrapper stat-icon-green">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card-widget stat-purple">
            <div class="stat-info">
                <div class="stat-label">Active Stores</div>
                <div class="stat-value font-mono"><?= (int)($stats['total_stores'] ?? 0) ?></div>
                <div class="stat-subtext">Live merchant sites</div>
            </div>
            <div class="stat-icon-wrapper stat-icon-purple">
                <i class="bi bi-shop"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="stat-card-widget stat-amber">
            <div class="stat-info">
                <div class="stat-label">Total Merchants</div>
                <div class="stat-value font-mono"><?= (int)($stats['total_merchants'] ?? 0) ?></div>
                <div class="stat-subtext"><?= (int)($stats['active_merchants'] ?? 0) ?> active accounts</div>
            </div>
            <div class="stat-icon-wrapper stat-icon-amber">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- 2. Active Tier Distribution Cards -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <h6 class="fw-800 text-dark mb-1"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Subscription Tier Breakdown</h6>
    </div>
    <?php foreach ($stats['plan_breakdown'] ?? [] as $plan): ?>
    <div class="col-md-4">
        <div class="card p-3 border shadow-sm" style="border-radius: 14px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-xs fw-800"><?= e($plan['badge'] ?? 'Tier') ?></span>
                <span class="fw-800 text-dark font-mono">₹<?= number_format($plan['price'], 0) ?>/mo</span>
            </div>
            <h5 class="fw-900 text-dark mb-1" style="font-size: 1.1rem;"><?= e($plan['name']) ?></h5>
            <div class="d-flex align-items-baseline gap-2">
                <span class="fs-3 fw-900 text-dark font-mono"><?= (int)$plan['subscriber_count'] ?></span>
                <span class="text-muted text-xs fw-600">active subscriber(s)</span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- 3. Recent Merchants & Quick Approvals Grid -->
<div class="row g-4 mb-4">
    <!-- Recent Merchant Registrations -->
    <div class="col-lg-8">
        <div class="table-responsive-wrapper h-100">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                <h6 class="fw-800 text-dark mb-0"><i class="bi bi-person-lines-fill text-primary me-2"></i>Recent Merchant Registrations</h6>
                <a href="<?= url('admin/merchants') ?>" class="btn btn-sm btn-outline-dark fw-700">View All Merchants</a>
            </div>

            <?php if (empty($recentMerchants)): ?>
                <div class="text-center py-5 text-muted bg-white">
                    <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                    <h5 class="empty-state-title">No merchant registrations yet</h5>
                    <p class="empty-state-desc">New merchant signups will appear here in real-time.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Merchant</th>
                                <th>Business Name</th>
                                <th>Plan Tier</th>
                                <th>Status</th>
                                <th>Joined Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentMerchants as $m): ?>
                            <tr>
                                <td>
                                    <div class="fw-800 text-dark"><?= e($m['name']) ?></div>
                                    <div class="text-muted text-xs font-mono"><?= e($m['email']) ?></div>
                                </td>
                                <td>
                                    <span class="text-dark fw-600 small"><?= e($m['business_name'] ?: 'Not configured') ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($m['plan_name'])): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-xs fw-700"><?= e($m['plan_name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted text-xs">&mdash;</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($m['status'] === 'active'): ?>
                                        <span class="badge-pill-custom badge-status-active"><span class="badge-pulse-dot"></span> Active</span>
                                    <?php elseif ($m['status'] === 'suspended'): ?>
                                        <span class="badge-pill-custom badge-status-danger"><span class="badge-pulse-dot"></span> Suspended</span>
                                    <?php else: ?>
                                        <span class="badge-pill-custom badge-status-pending"><span class="badge-pulse-dot"></span> Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted text-xs">
                                    <?= date('M d, Y', strtotime($m['registered_at'] ?? $m['created_at'])) ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= url('admin/merchants/' . $m['id']) ?>" class="btn btn-sm btn-outline-primary fw-700" style="padding: 0.25rem 0.65rem;">
                                        Manage
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Action Launchpad & Pending Payments -->
    <div class="col-lg-4">
        <!-- Pending Verification Queue -->
        <div class="card p-4 border shadow-sm mb-4 bg-white" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-800 text-dark mb-0"><i class="bi bi-clock-history text-warning me-2"></i>Pending Bank Approvals</h6>
                <a href="<?= url('admin/payments') ?>" class="text-primary text-xs fw-800 text-decoration-none">View All (<?= count($pendingPayments) ?>)</a>
            </div>

            <?php if (empty($pendingPayments)): ?>
                <div class="text-center py-3 text-muted text-xs">
                    <i class="bi bi-check-circle-fill text-success fs-3 d-block mb-2"></i>
                    All bank/UPI payments verified!
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-2">
                    <?php foreach ($pendingPayments as $pay): ?>
                    <div class="p-2 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-700 text-dark text-xs"><?= e($pay['business_name'] ?: $pay['user_name']) ?></div>
                            <div class="text-muted text-xs font-mono">UTR: <?= e($pay['payment_proof_utr'] ?? 'N/A') ?></div>
                        </div>
                        <div class="text-end">
                            <div class="fw-800 text-primary text-xs font-mono">₹<?= number_format($pay['amount'], 2) ?></div>
                            <a href="<?= url('admin/payments') ?>" class="btn btn-xs btn-primary fw-700" style="font-size: 0.65rem; padding: 0.15rem 0.45rem;">Verify</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- System Fast Actions -->
        <div class="card p-4 border shadow-sm bg-white" style="border-radius: 16px;">
            <h6 class="fw-800 text-dark mb-3"><i class="bi bi-lightning-charge-fill text-primary me-2"></i>Executive Shortcuts</h6>
            <div class="d-flex flex-column gap-2">
                <a href="<?= url('admin/announcements') ?>" class="btn btn-light border text-start d-flex align-items-center justify-content-between py-2 shadow-xs" style="border-radius: 10px;">
                    <span class="fw-700 text-dark small"><i class="bi bi-megaphone-fill text-primary me-2"></i> Post Global Announcement</span>
                    <i class="bi bi-chevron-right text-muted text-xs"></i>
                </a>
                <a href="<?= url('admin/support') ?>" class="btn btn-light border text-start d-flex align-items-center justify-content-between py-2 shadow-xs" style="border-radius: 10px;">
                    <span class="fw-700 text-dark small"><i class="bi bi-headset text-success me-2"></i> Merchant Support Desk</span>
                    <i class="bi bi-chevron-right text-muted text-xs"></i>
                </a>
                <a href="<?= url('admin/settings') ?>" class="btn btn-light border text-start d-flex align-items-center justify-content-between py-2 shadow-xs" style="border-radius: 10px;">
                    <span class="fw-700 text-dark small"><i class="bi bi-gear-fill text-secondary me-2"></i> Platform Global Settings</span>
                    <i class="bi bi-chevron-right text-muted text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php View::endSection(); ?>
