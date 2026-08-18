<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>SaaS Subscription<?php View::endSection();
View::section('page_title'); ?>BW Store Subscription & Plans<?php View::endSection();

View::section('content'); ?>

<?php
    $hasPendingUpi = false;
    foreach ($payments ?? [] as $p) {
        if ($p['status'] === 'pending_verification') {
            $hasPendingUpi = $p;
            break;
        }
    }

    $isTrial = ($subscription['status'] ?? '') === 'trialing';
    $currentPlanId = $subscription['plan_id'] ?? 2;
    $currentPlanName = $subscription['plan_name'] ?? 'BW Store Growth';
    $currentPlanPrice = $subscription['plan_price'] ?? 999;
    $currentPlanBadge = $subscription['plan_badge'] ?? ($isTrial ? 'Free Trial' : 'Recommended');
    $maxProducts = (int)($subscription['plan_max_products'] ?? 0);
    $productsUsed = $productCount ?? 0;
    
    // Calculate days remaining
    $daysLeft = 0;
    $endDate = !empty($subscription['current_period_end']) ? $subscription['current_period_end'] : date('Y-m-d H:i:s', strtotime('+7 days'));
    $diffSecs = strtotime($endDate) - time();
    if ($diffSecs > 0) {
        $daysLeft = ceil($diffSecs / 86400);
    }
?>

<?php if ($isTrial): ?>
<!-- 7-Day Free Trial Notice Banner -->
<div class="alert alert-info border border-info shadow-sm mb-4 bg-primary-subtle">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary text-white p-2 fs-4">
                <i class="bi bi-gift-fill"></i>
            </div>
            <div>
                <h6 class="fw-800 text-dark mb-0">🎉 7-Day Free Trial is Currently Active (<?= (int)$daysLeft ?> Days Left)!</h6>
                <div class="text-secondary small">You have full access to add products and accept customer orders. Choose a plan anytime to continue smoothly after your trial.</div>
            </div>
        </div>
        <a href="<?= url('dashboard/subscription/checkout') ?>" class="btn btn-primary btn-sm px-4 fw-800 shadow-sm">
            Choose Plan & Upgrade &rarr;
        </a>
    </div>
</div>
<?php endif; ?>

<?php if ($hasPendingUpi): ?>
<div class="alert alert-warning border border-warning shadow-sm mb-4">
    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-hourglass-split fs-3 text-warning-emphasis"></i>
        <div>
            <h6 class="fw-800 text-dark mb-1">⏳ UPI Payment Verification in Progress</h6>
            <div class="small text-secondary">
                Aapka UPI Payment UTR (<code><?= e($hasPendingUpi['transaction_ref']) ?></code>) ₹<?= number_format($hasPendingUpi['amount'], 2) ?> submit ho chuka hai. Admin receipt verify karke store turant activate kar dega.
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <!-- Active Plan Card -->
    <div class="col-lg-6">
        <div class="card border-primary h-100 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge bg-primary px-3 py-1 mb-2 text-uppercase fw-800"><?= e($currentPlanBadge) ?></span>
                        <h3 class="fw-900 text-dark mb-0"><?= e($currentPlanName) ?></h3>
                    </div>
                    <div class="text-end">
                        <div class="fs-2 fw-900 text-primary">
                            <?= $isTrial ? 'FREE' : '₹' . number_format($currentPlanPrice, 0) ?>
                        </div>
                        <div class="text-secondary text-xs fw-600"><?= $isTrial ? '7-Day Trial' : 'per month' ?></div>
                    </div>
                </div>

                <p class="text-secondary small mb-4">
                    <?= e($subscription['plan_description'] ?? 'Full platform access with 0% sales commission and high-converting storefront.') ?>
                </p>

                <div class="bg-light rounded-3 p-3 mb-4 border">
                    <div class="d-flex justify-content-between py-1 border-bottom small">
                        <span class="text-secondary fw-600">Subscription Status:</span>
                        <span class="fw-800 text-success text-uppercase">
                            <i class="bi bi-check-circle-fill me-1"></i> <?= $isTrial ? '7-DAY FREE TRIAL' : e($subscription['status'] ?? 'ACTIVE') ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom small">
                        <span class="text-secondary fw-600">Time Remaining:</span>
                        <span class="fw-800 text-primary"><?= (int)$daysLeft ?> Days Left</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom small">
                        <span class="text-secondary fw-600">Trial / Renewal Date:</span>
                        <span class="fw-700 text-dark"><?= date('M d, Y', strtotime($endDate)) ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-1 small">
                        <span class="text-secondary fw-600">Sales Commission:</span>
                        <span class="fw-800 text-success">0% (Keep 100% Profit)</span>
                    </div>
                </div>

                <a href="<?= url('dashboard/subscription/checkout') ?>" class="btn btn-primary w-100 py-3 fw-800 shadow-sm">
                    <i class="bi bi-credit-card-2-front me-2"></i> <?= $isTrial ? 'Upgrade / Select Plan (From ₹499)' : 'Renew / Change Plan (UPI & Cards)' ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Plan Usage Meters & Feature Highlights -->
    <div class="col-lg-6">
        <div class="card h-100 bg-light border">
            <div class="card-body p-4">
                <h5 class="fw-800 text-dark mb-3"><i class="bi bi-speedometer2 text-primary me-2"></i>Plan Usage & Limits</h5>
                
                <!-- Product Limit Meter -->
                <div class="bg-white p-3 rounded-3 border mb-3">
                    <div class="d-flex justify-content-between text-xs fw-800 text-dark mb-1">
                        <span>Product Limit Usage</span>
                        <span><?= (int)$productsUsed ?> / <?= $maxProducts > 0 ? (int)$maxProducts : 'Unlimited' ?></span>
                    </div>
                    <?php 
                        $percent = ($maxProducts > 0) ? min(100, round(($productsUsed / $maxProducts) * 100)) : 15;
                    ?>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar <?= $percent > 90 ? 'bg-danger' : 'bg-primary' ?>" role="progressbar" style="width: <?= $percent ?>%"></div>
                    </div>
                    <div class="text-secondary text-xs mt-1">
                        <?= $maxProducts > 0 ? 'Starter plan allows up to 10 products. Upgrade to Growth for unlimited.' : 'You have unlimited product catalog capacity.' ?>
                    </div>
                </div>

                <h6 class="fw-800 text-dark mb-2 small"><i class="bi bi-stars text-primary me-1"></i>Included in Your Package</h6>
                <ul class="list-unstyled mb-0">
                    <li class="py-1 border-bottom d-flex align-items-center text-xs text-dark"><i class="bi bi-check-circle-fill text-success me-2"></i> <strong>0% Platform Commission</strong> on customer sales</li>
                    <li class="py-1 border-bottom d-flex align-items-center text-xs text-dark"><i class="bi bi-check-circle-fill text-success me-2"></i> Direct UPI QR + Razorpay Payments Connect</li>
                    <li class="py-1 border-bottom d-flex align-items-center text-xs text-dark"><i class="bi bi-check-circle-fill text-success me-2"></i> Storefront Themes (Modern, Fashion & Business)</li>
                    <li class="py-1 border-bottom d-flex align-items-center text-xs text-dark"><i class="bi bi-check-circle-fill text-success me-2"></i> Real-Time Sales & Revenue Analytics</li>
                    <li class="py-1 d-flex align-items-center text-xs text-dark"><i class="bi bi-check-circle-fill text-success me-2"></i> 24/7 Dedicated Ticket Support</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Available SaaS Tier Upgrade Options -->
<div class="card mb-4">
    <div class="card-body p-4">
        <h5 class="fw-800 text-dark mb-1"><i class="bi bi-grid text-primary me-2"></i>All SaaS Tier Plans</h5>
        <p class="text-secondary small mb-4">Switch or upgrade to any plan at any time. Save up to ₹1,000 Flat on yearly plans!</p>

        <div class="row g-3">
            <?php foreach ($allPlans as $p): ?>
                <?php 
                    $isCurrent = ($p['id'] == $currentPlanId && !$isTrial);
                ?>
                <div class="col-md-4">
                    <div class="card p-3 border h-100 rounded-3 <?= $isCurrent ? 'border-primary bg-primary-subtle' : 'bg-white' ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-800 text-dark mb-0"><?= e($p['name']) ?></h6>
                            <?php if ($isCurrent): ?>
                                <span class="badge bg-primary text-white text-xs">CURRENT</span>
                            <?php elseif (!empty($p['badge'])): ?>
                                <span class="badge bg-secondary-subtle text-dark border text-xs fw-700"><?= e($p['badge']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="fs-4 fw-900 text-primary mb-1">₹<?= number_format($p['price'], 0) ?> <span class="text-secondary text-xs fw-normal">/ mo</span></div>
                        
                        <?php if (!empty($p['yearly_discount']) && (float)$p['yearly_discount'] > 0): ?>
                            <div class="badge bg-success-subtle text-success text-xs mb-2">Save ₹<?= number_format($p['yearly_discount'], 0) ?> Flat on Yearly (₹<?= number_format($p['yearly_price'], 0) ?>/yr)</div>
                        <?php endif; ?>

                        <p class="text-secondary text-xs mb-3"><?= e($p['description'] ?? '') ?></p>
                        
                        <div class="mt-auto">
                            <?php if ($isCurrent): ?>
                                <a href="<?= url('dashboard/subscription/checkout?plan_id=' . $p['id']) ?>" class="btn btn-outline-primary btn-sm w-100 fw-700">
                                    Renew This Plan
                                </a>
                            <?php else: ?>
                                <a href="<?= url('dashboard/subscription/checkout?plan_id=' . $p['id']) ?>" class="btn btn-primary btn-sm w-100 fw-700">
                                    Switch to <?= e($p['name']) ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Payment History Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="p-3 border-bottom">
            <h5 class="fw-800 text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Subscription Billing History</h5>
        </div>

        <?php if (empty($payments)): ?>
            <div class="p-4 text-center text-secondary">No previous subscription invoices.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Invoice / Payment ID</th>
                            <th>Method</th>
                            <th>Transaction Ref / UTR</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $pay): ?>
                        <tr>
                            <td><code><?= e($pay['gateway_payment_id'] ?: 'INV-' . $pay['id']) ?></code></td>
                            <td><span class="badge bg-secondary"><?= e($pay['payment_method'] ?? 'ONLINE') ?></span></td>
                            <td><span class="text-dark font-monospace text-xs fw-700"><?= e($pay['transaction_ref'] ?? '&mdash;') ?></span></td>
                            <td class="text-secondary text-xs"><?= date('M d, Y H:i', strtotime($pay['created_at'])) ?></td>
                            <td class="fw-800 text-dark">₹<?= number_format($pay['amount'], 2) ?></td>
                            <td>
                                <?php if ($pay['status'] === 'paid'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">PAID</span>
                                <?php elseif ($pay['status'] === 'pending_verification'): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning">PENDING VERIFICATION</span>
                                <?php elseif ($pay['status'] === 'rejected'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">REJECTED</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= strtoupper(e($pay['status'])) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php View::endSection(); ?>
