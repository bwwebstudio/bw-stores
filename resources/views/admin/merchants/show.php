<?php

use App\Core\View;
View::layout('layouts.admin');

View::section('title'); ?>Manage Merchant: <?= e($merchant['name']) ?><?php View::endSection();
View::section('page_title'); ?>Merchant: <?= e($merchant['name']) ?><?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <a href="<?= url('admin/merchants') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Merchants
    </a>
    <div>
        <?php if ($merchant['status'] === 'active'): ?>
            <span class="badge bg-success fs-6">STATUS: ACTIVE</span>
        <?php elseif ($merchant['status'] === 'suspended'): ?>
            <span class="badge bg-danger fs-6">STATUS: SUSPENDED</span>
        <?php else: ?>
            <span class="badge bg-warning text-dark fs-6">STATUS: PENDING</span>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Left 6 Columns: Profile Details -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-700 mb-3"><i class="bi bi-person-lines-fill text-primary me-2"></i>Account & Identity</h5>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Full Name:</span>
                        <strong><?= e($merchant['name']) ?></strong>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Email:</span>
                        <span><?= e($merchant['email']) ?></span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Mobile Number:</span>
                        <span><?= e($merchant['mobile'] ?: 'Not provided') ?></span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Business Name:</span>
                        <strong><?= e($merchant['business_name'] ?: 'Not set') ?></strong>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Category:</span>
                        <span><?= e($merchant['business_category'] ?: 'Uncategorized') ?></span>
                    </li>
                    <li class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Registration Date:</span>
                        <span><?= date('M d, Y H:i', strtotime($merchant['created_at'])) ?></span>
                    </li>
                    <li class="d-flex justify-content-between py-2">
                        <span class="text-muted">Email Verified:</span>
                        <span class="badge bg-<?= $merchant['email_verified_at'] ? 'success' : 'warning text-dark' ?>">
                            <?= $merchant['email_verified_at'] ? 'VERIFIED' : 'UNVERIFIED' ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Storefront Details -->
        <?php if ($store): ?>
        <div class="card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-700 mb-3"><i class="bi bi-shop text-primary me-2"></i>Associated Storefront</h5>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-700 mb-0"><?= e($store['name']) ?></h6>
                        <code class="text-xs">/store/<?= e($store['slug']) ?></code>
                    </div>
                    <a href="<?= url('store/' . $store['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Visit Store
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right 6 Columns: Actions & Subscription Override -->
    <div class="col-lg-6">
        <!-- Subscription Control -->
        <div class="card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-700 mb-3"><i class="bi bi-credit-card-2-back text-primary me-2"></i>Subscription Control</h5>

                <div class="bg-light p-3 rounded mb-3">
                    <div class="d-flex justify-content-between py-1 small border-bottom">
                        <span class="text-muted">Plan:</span>
                        <strong>BW Store (₹999/mo)</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1 small border-bottom">
                        <span class="text-muted">Current Status:</span>
                        <span class="badge bg-success"><?= strtoupper(e($subscription['status'] ?? 'ACTIVE')) ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-1 small">
                        <span class="text-muted">Valid Until:</span>
                        <strong><?= !empty($subscription['current_period_end']) ? date('M d, Y', strtotime($subscription['current_period_end'])) : 'N/A' ?></strong>
                    </div>
                </div>

                <form method="POST" action="<?= url('admin/merchants/' . $merchant['id'] . '/extend') ?>" data-loading>
                    <?= csrf_field() ?>
                    <label class="form-label small fw-600">Extend Plan Validity / Grant Free Trial (Admin Override)</label>
                    <div class="input-group mb-2">
                        <input type="number" id="extendDaysInput" name="days" class="form-control" value="7" min="1" max="365">
                        <span class="input-group-text">days</span>
                        <button type="submit" class="btn btn-outline-primary fw-600">Apply Extension</button>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="document.getElementById('extendDaysInput').value = 7;">+7 Days Trial</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="document.getElementById('extendDaysInput').value = 14;">+14 Days</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="document.getElementById('extendDaysInput').value = 30;">+30 Days (1 Mo)</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Password Management (Admin Override & Reset) -->
        <div class="card mb-4 border-warning">
            <div class="card-body p-4">
                <h5 class="fw-700 mb-2 text-dark"><i class="bi bi-key-fill text-warning me-2"></i>Reset Merchant Password</h5>
                <p class="text-muted small mb-3">Manually set or generate a new login password for this merchant when requested via Support / WhatsApp.</p>

                <form method="POST" action="<?= url('admin/merchants/' . $merchant['id'] . '/reset-password') ?>" data-loading>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label small fw-700 text-dark">New Password for Merchant</label>
                        <div class="input-group">
                            <input type="text" id="merchantNewPwd" name="new_password" class="form-control font-monospace" placeholder="Enter new password (min 6 chars)" required minlength="6">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="generateRandomMerchantPwd()">
                                <i class="bi bi-shuffle me-1"></i> Generate
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-700 py-2 text-dark" onclick="return confirm('Update login password for this merchant now?');">
                        <i class="bi bi-check2-circle me-1"></i> Set New Password & Save
                    </button>
                </form>
            </div>
        </div>

        <!-- Administrative Actions (Enable/Disable & Delete) -->
        <div class="card border-danger">
            <div class="card-body p-4">
                <h5 class="fw-700 mb-3 text-danger"><i class="bi bi-shield-slash me-2"></i>Store Controls & Danger Zone</h5>

                <!-- 1-Click Store Enable/Disable Toggle -->
                <div class="p-3 bg-light rounded-3 border mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong class="text-dark">Storefront Public Access:</strong>
                            <div class="text-muted text-xs">
                                Current Status: <span class="badge bg-<?= ($store['status'] ?? '') === 'active' ? 'success' : 'danger' ?>"><?= strtoupper(e($store['status'] ?? 'INACTIVE')) ?></span>
                            </div>
                        </div>
                        <form method="POST" action="<?= url('admin/merchants/' . $merchant['id'] . '/toggle-store') ?>">
                            <?= csrf_field() ?>
                            <?php if (($store['status'] ?? '') === 'active'): ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger fw-600" onclick="return confirm('Disable and shut down this storefront immediately?');">
                                    <i class="bi bi-toggle-on fs-5 align-middle"></i> Disable Store
                                </button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-sm btn-success fw-600">
                                    <i class="bi bi-toggle-off fs-5 align-middle"></i> Enable Store
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Permanent Delete Merchant -->
                <form method="POST" action="<?= url('admin/merchants/' . $merchant['id'] . '/delete') ?>" onsubmit="return confirm('⚠️ FINAL CONFIRMATION:\nAre you 100% sure you want to permanently delete this merchant and wipe all their store data, products, orders and subscription records? This cannot be recovered.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger w-100 fw-700 py-2">
                        <i class="bi bi-trash3-fill me-1"></i> Delete Merchant & All Store Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function generateRandomMerchantPwd() {
    var chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%";
    var pass = "";
    for (var i = 0; i < 10; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    var input = document.getElementById('merchantNewPwd');
    if (input) {
        input.value = pass;
        input.type = 'text';
    }
}
</script>

<?php View::endSection(); ?>
