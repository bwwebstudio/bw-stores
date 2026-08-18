<?php

use App\Core\View;
View::layout('layouts.admin');

View::section('title'); ?>Platform Settings<?php View::endSection();
View::section('page_title'); ?>Global Platform Settings<?php View::endSection();

View::section('content'); ?>

<form method="POST" action="<?= url('admin/settings') ?>" data-loading>
    <?= csrf_field() ?>

    <div class="row g-4">
        <div class="col-lg-6">
            <!-- Platform Identity -->
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-700 mb-3"><i class="bi bi-gear text-primary me-2"></i>General SaaS Configuration</h5>

                    <div class="mb-3">
                        <label class="form-label">Platform Product Name</label>
                        <input type="text" name="platform_name" class="form-control" value="<?= e($settings['platform_name'] ?? 'BW Store') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Parent Company</label>
                        <input type="text" name="company_name" class="form-control" value="<?= e($settings['company_name'] ?? 'BW Web Studio') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="platform_tagline" class="form-control" value="<?= e($settings['platform_tagline'] ?? 'Your Store. Your Brand. Your Sales.') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Default Monthly Subscription Fee (₹)</label>
                        <input type="number" step="0.01" name="subscription_price" class="form-control fw-700" value="<?= e($settings['subscription_price'] ?? '999.00') ?>" required>
                        <div class="text-muted text-xs mt-1">Single subscription model price. Stored in config/database.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <!-- Global Support & Contact -->
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-700 mb-3"><i class="bi bi-headset text-primary me-2"></i>Global Support & Notices</h5>

                    <div class="mb-3">
                        <label class="form-label">Master Support Email</label>
                        <input type="email" name="support_email" class="form-control" value="<?= e($settings['support_email'] ?? 'support@bwwebstudio.com') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Admin Notification Email</label>
                        <input type="email" name="admin_notify_email" class="form-control" value="<?= e($settings['admin_notify_email'] ?? 'admin@bwwebstudio.com') ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Platform Maintenance Notice</label>
                        <textarea name="maintenance_notice" rows="2" class="form-control" placeholder="Optional banner shown to all merchants..."><?= e($settings['maintenance_notice'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Admin Razorpay & Payout Settings -->
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-700 mb-3"><i class="bi bi-wallet2 text-success me-2"></i>Admin SaaS Payment Collection (Razorpay & UPI)</h5>
                    <p class="text-muted small mb-3">Merchant subscription fees (₹999/mo) will be routed directly to this Razorpay/UPI account.</p>

                    <div class="mb-3">
                        <label class="form-label">Admin Razorpay Key ID</label>
                        <input type="text" name="admin_razorpay_key_id" class="form-control" placeholder="rzp_live_xxxxxxxxxxxxxx" value="<?= e($settings['admin_razorpay_key_id'] ?? '') ?>">
                        <span class="text-muted text-xs">From Razorpay Dashboard &rarr; Settings &rarr; API Keys</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Admin Razorpay Key Secret</label>
                        <input type="password" name="admin_razorpay_key_secret" class="form-control" placeholder="••••••••••••••••••••••••" value="<?= e($settings['admin_razorpay_key_secret'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Admin UPI ID / VPA (Optional Direct UPI)</label>
                        <input type="text" name="admin_upi_id" class="form-control" placeholder="bwwebstudio@okhdfcbank" value="<?= e($settings['admin_upi_id'] ?? '') ?>">
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-700 shadow">
                        <i class="bi bi-check-lg me-1"></i> Save Platform Settings
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<?php View::endSection(); ?>
