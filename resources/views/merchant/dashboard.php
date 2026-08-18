<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Merchant Dashboard — BW Store<?php View::endSection();
View::section('page_title'); ?>Merchant Overview<?php View::endSection();

View::section('content'); ?>

<!-- 1. Live Storefront Link & Share Hero Card -->
<?php if (!empty($store)): ?>
<div class="card mb-4 border-0 shadow-lg overflow-hidden" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); color: #FFFFFF; border-radius: 18px;">
    <div class="card-body p-4 position-relative">
        <div class="row align-items-center g-3">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 text-xs fw-800 d-inline-flex align-items-center gap-1">
                        <span class="badge-pulse-dot" style="background:#10B981;"></span> LIVE STOREFRONT
                    </span>
                    <span class="text-secondary small" style="color: #94A3B8 !important;">Your Public Store URL:</span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    <h4 class="fw-900 mb-0 text-white font-mono" id="storeUrlText" style="letter-spacing: -0.02em; font-size: 1.25rem;">
                        <?= url('store/' . $store['slug']) ?>
                    </h4>
                </div>
                <div class="text-xs" style="color: #94A3B8 !important;">
                    Subdomain: <strong class="text-light">https://<?= e($store['slug']) ?>.bwstore.in</strong> &bull; Custom domain ready &bull; Zero sales cut
                </div>
            </div>

            <div class="col-lg-5 text-lg-end d-flex gap-2 justify-content-lg-end flex-wrap">
                <button type="button" class="btn btn-light btn-sm fw-700 px-3 shadow-sm" onclick="copyStoreUrl()" id="copyBtn" style="border-radius: 8px;">
                    <i class="bi bi-clipboard me-1 text-primary"></i> Copy Link
                </button>
                <a href="https://wa.me/?text=Check%20out%20my%20new%20online%20store%20here%3A%20<?= urlencode(url('store/' . $store['slug'])) ?>" target="_blank" class="btn btn-success btn-sm fw-700 px-3 shadow-sm" style="border-radius: 8px;">
                    <i class="bi bi-whatsapp me-1"></i> Share on WhatsApp
                </a>
                <a href="<?= url('store/' . $store['slug']) ?>" target="_blank" class="btn btn-primary-gradient btn-sm fw-700 px-3 shadow-sm" style="border-radius: 8px;">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Open Live Store
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- 2. Subscription Status & Quick Launchpad Grid -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card h-100 p-4 border shadow-sm" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 mb-1 text-xs fw-800">MEMBERSHIP PLAN</span>
                    <h5 class="fw-800 text-dark mb-0">BW Store Growth Tier</h5>
                </div>
                <span class="badge-pill-custom badge-status-active">
                    <span class="badge-pulse-dot"></span> ACTIVE (0% COMMISSION)
                </span>
            </div>

            <p class="text-secondary small mb-3">
                Zero platform sales commission on all your customer orders. Your storefront is active and taking direct UPI and card payments.
            </p>

            <div class="bg-light rounded-3 p-3 mb-3 border">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-700 text-dark small"><i class="bi bi-clock-history text-danger me-1"></i> Plan Expiration Countdown:</span>
                    <strong class="text-danger fs-6 font-monospace" id="dashboardCountdownDisplay"><?= $daysRemaining ?> Days Remaining</strong>
                </div>
                <?php
                $progressPercent = max(5, min(100, (int)(($daysRemaining / 30) * 100)));
                ?>
                <div class="progress mb-2" style="height: 7px; background-color: #E2E8F0; border-radius: 999px;">
                    <div class="progress-bar" role="progressbar" style="width: <?= $progressPercent ?>%; background: linear-gradient(90deg, #2563EB, #7C3AED); border-radius: 999px;"></div>
                </div>
                <div class="d-flex justify-content-between text-xs text-muted">
                    <span>Valid until: <strong><?= $expiryDate ?: date('d M Y', strtotime('+30 days')) ?></strong></span>
                    <span>Renewal Rate: <strong>₹999.00 / mo</strong></span>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="<?= url('dashboard/subscription') ?>" class="btn btn-sm btn-outline-primary fw-700">
                    <i class="bi bi-arrow-repeat me-1"></i> Manage / Upgrade Subscription
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Action Launchpad -->
    <div class="col-lg-4">
        <div class="card h-100 p-4 border shadow-sm bg-white" style="border-radius: 16px;">
            <h6 class="fw-800 text-dark mb-3"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Launchpad</h6>
            <div class="d-flex flex-column gap-2">
                <a href="<?= url('dashboard/products/create') ?>" class="btn btn-light border text-start d-flex align-items-center justify-content-between py-2 shadow-xs" style="border-radius: 10px;">
                    <span class="fw-700 text-dark small"><i class="bi bi-plus-circle-fill text-primary me-2"></i> Add New Product</span>
                    <i class="bi bi-chevron-right text-muted text-xs"></i>
                </a>
                <a href="<?= url('dashboard/store-design') ?>" class="btn btn-light border text-start d-flex align-items-center justify-content-between py-2 shadow-xs" style="border-radius: 10px;">
                    <span class="fw-700 text-dark small"><i class="bi bi-palette-fill text-purple me-2"></i> Customize Theme</span>
                    <i class="bi bi-chevron-right text-muted text-xs"></i>
                </a>
                <a href="<?= url('dashboard/coupons') ?>" class="btn btn-light border text-start d-flex align-items-center justify-content-between py-2 shadow-xs" style="border-radius: 10px;">
                    <span class="fw-700 text-dark small"><i class="bi bi-ticket-perforated-fill text-success me-2"></i> Promo Coupon</span>
                    <i class="bi bi-chevron-right text-muted text-xs"></i>
                </a>
                <a href="<?= url('dashboard/payments') ?>" class="btn btn-light border text-start d-flex align-items-center justify-content-between py-2 shadow-xs" style="border-radius: 10px;">
                    <span class="fw-700 text-dark small"><i class="bi bi-credit-card-2-front text-dark me-2"></i> Payment Gateway</span>
                    <i class="bi bi-chevron-right text-muted text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 3. Key Store Metrics Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card-widget stat-primary">
            <div class="stat-info">
                <div class="stat-label">Today's Revenue</div>
                <div class="stat-value">₹<?= number_format($todaySales ?? 0, 2) ?></div>
                <div class="stat-subtext">Direct to bank</div>
            </div>
            <div class="stat-icon-wrapper stat-icon-blue">
                <i class="bi bi-currency-rupee"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card-widget stat-emerald">
            <div class="stat-info">
                <div class="stat-label">Total Orders</div>
                <div class="stat-value"><?= (int)($totalOrders ?? 0) ?></div>
                <div class="stat-subtext">Customer purchases</div>
            </div>
            <div class="stat-icon-wrapper stat-icon-green">
                <i class="bi bi-bag-check"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card-widget stat-purple">
            <div class="stat-info">
                <div class="stat-label">Listed Products</div>
                <div class="stat-value"><?= (int)($totalProducts ?? 0) ?></div>
                <div class="stat-subtext">Active in catalog</div>
            </div>
            <div class="stat-icon-wrapper stat-icon-purple">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card-widget stat-amber">
            <div class="stat-info">
                <div class="stat-label">Platform Sales Cut</div>
                <div class="stat-value text-success">0%</div>
                <div class="stat-subtext">100% profit is yours</div>
            </div>
            <div class="stat-icon-wrapper stat-icon-amber">
                <i class="bi bi-percent"></i>
            </div>
        </div>
    </div>
</div>

<!-- 4. Recent Customer Orders Table -->
<div class="table-responsive-wrapper mb-4">
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
        <h5 class="fw-800 text-dark mb-0"><i class="bi bi-receipt text-primary me-2"></i>Recent Orders</h5>
        <a href="<?= url('dashboard/orders') ?>" class="btn btn-sm btn-outline-dark fw-700">View All Orders</a>
    </div>

    <?php if (empty($recentOrders)): ?>
        <div class="text-center py-5 text-muted bg-white">
            <div class="empty-state-icon">
                <i class="bi bi-receipt"></i>
            </div>
            <h5 class="empty-state-title">No customer orders placed yet</h5>
            <p class="empty-state-desc">Share your store link on WhatsApp, Instagram, and social media to start receiving customer orders.</p>
            <?php if (!empty($store)): ?>
            <button type="button" class="btn btn-primary btn-sm px-4 fw-700" onclick="copyStoreUrl()">
                <i class="bi bi-clipboard me-1"></i> Copy Store Link
            </button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $ord): ?>
                    <tr>
                        <td class="fw-800 font-mono text-dark">#<?= e($ord['order_number']) ?></td>
                        <td class="text-muted text-xs"><?= date('M d, H:i', strtotime($ord['created_at'])) ?></td>
                        <td>
                            <div class="fw-700 text-dark"><?= e($ord['customer_name']) ?></div>
                            <div class="text-muted text-xs"><?= e($ord['customer_mobile']) ?></div>
                        </td>
                        <td class="fw-800 text-dark">₹<?= number_format($ord['total'], 2) ?></td>
                        <td>
                            <span class="badge bg-<?= $ord['payment_status'] === 'PAID' ? 'success' : 'warning text-dark' ?>-subtle border px-2 py-1 text-xs fw-700">
                                <?= e($ord['payment_status']) ?> (<?= e($ord['payment_method']) ?>)
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 text-xs fw-700"><?= e($ord['order_status']) ?></span>
                        </td>
                        <td class="text-end">
                            <a href="<?= url('dashboard/orders/' . $ord['id']) ?>" class="btn btn-sm btn-outline-primary fw-700" style="padding: 0.25rem 0.65rem;">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
function copyStoreUrl() {
    var url = "<?= !empty($store) ? url('store/' . $store['slug']) : '' ?>";
    navigator.clipboard.writeText(url).then(function() {
        var btn = document.getElementById('copyBtn');
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg me-1 text-success"></i> Copied!';
        setTimeout(function() {
            btn.innerHTML = orig;
        }, 2000);
    });
}
</script>

<?php View::endSection(); ?>
