<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Account Settings<?php View::endSection();
View::section('page_title'); ?>Merchant Profile & Security<?php View::endSection();

View::section('content'); ?>

<div class="row g-4">
    <!-- Profile & Business Details -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-person-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-800 text-dark mb-0">Merchant Profile Details</h5>
                        <p class="text-secondary small mb-0">Manage your contact and registered business name</p>
                    </div>
                </div>

                <form method="POST" action="<?= url('dashboard/settings/profile') ?>" data-loading>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control fw-600" value="<?= e($user['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">Registered Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control bg-light text-muted fw-600" value="<?= e($user['email']) ?>" readonly>
                        </div>
                        <div class="text-muted text-xs mt-1">To change email address, contact Administrator Support.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">Mobile / WhatsApp Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                            <input type="tel" name="mobile" class="form-control" placeholder="e.g. 9876543210" value="<?= e($user['mobile'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">Business / Brand Name</label>
                        <input type="text" name="business_name" class="form-control" placeholder="e.g. Aura Luxe Apparel" value="<?= e($merchant['business_name'] ?? '') ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-700 text-xs text-dark">Business Category</label>
                        <input type="text" name="business_category" class="form-control" placeholder="e.g. Fashion, Electronics, Gourmet Food" value="<?= e($merchant['business_category'] ?? '') ?>">
                    </div>

                    <button type="submit" class="btn btn-primary fw-800 px-4 py-2 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Save Profile Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Security & Merchant Password Change -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-shield-lock-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-800 text-dark mb-0">Change Your Password</h5>
                        <p class="text-secondary small mb-0">Update your login password securely anytime</p>
                    </div>
                </div>

                <form method="POST" action="<?= url('dashboard/settings/password') ?>" data-loading>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">Current Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                            <input type="password" id="curPwd" name="current_password" class="form-control <?= has_error('current_password') ? 'is-invalid' : '' ?>" placeholder="Enter current password" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVis('curPwd', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <?php if (has_error('current_password')): ?>
                        <div class="text-danger text-xs mt-1 fw-600"><?= e(error('current_password')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">New Password (Min 8 Characters) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-key"></i></span>
                            <input type="password" id="newPwd" name="new_password" class="form-control <?= has_error('new_password') ? 'is-invalid' : '' ?>" placeholder="Enter new strong password" required minlength="8">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVis('newPwd', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <?php if (has_error('new_password')): ?>
                        <div class="text-danger text-xs mt-1 fw-600"><?= e(error('new_password')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-700 text-xs text-dark">Confirm New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-shield-check"></i></span>
                            <input type="password" id="confPwd" name="confirm_password" class="form-control <?= has_error('confirm_password') ? 'is-invalid' : '' ?>" placeholder="Repeat new password" required minlength="8">
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVis('confPwd', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <?php if (has_error('confirm_password')): ?>
                        <div class="text-danger text-xs mt-1 fw-600"><?= e(error('confirm_password')) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-4 text-xs text-secondary">
                        <div class="fw-700 text-dark mb-1"><i class="bi bi-shield-shaded text-primary me-1"></i> Password Security Tips:</div>
                        <ul class="mb-0 ps-3">
                            <li>Minimum 8 characters length</li>
                            <li>Include letters, numbers and special symbols</li>
                            <li>Never share your merchant dashboard credentials</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn btn-dark fw-800 px-4 py-2 shadow-sm">
                        <i class="bi bi-key-fill me-1"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVis(inputId, btn) {
    var input = document.getElementById(inputId);
    var icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>

<?php View::endSection(); ?>
