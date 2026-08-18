<?php

use App\Core\View;
View::layout('layouts.auth');

View::section('title'); ?>Super Admin Login — BW Store<?php View::endSection();

View::section('content'); ?>

<div class="auth-card" style="border-top: 4px solid var(--color-primary); border-radius: 20px;">
    <div class="auth-header text-center mb-4">
        <div class="logo-mark mx-auto mb-3" style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); width: 56px; height: 56px; border-radius: 16px; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.25);">
            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 1.65rem;"></i>
        </div>
        <h3 class="fw-900 text-dark mb-1" style="font-size: 1.6rem; letter-spacing: -0.03em;">Super Admin Portal</h3>
        <p class="text-secondary small mb-0 fw-500">Sign in to access platform management and system controls.</p>
    </div>

    <!-- Alert / Error Messages -->
    <?php if ($generalError = error('general')): ?>
    <div class="alert alert-danger mb-3 py-2 px-3 d-flex align-items-center gap-2 text-sm rounded-3">
        <i class="bi bi-exclamation-triangle-fill text-danger flex-shrink-0"></i>
        <div><?= e($generalError) ?></div>
    </div>
    <?php endif; ?>

    <?php if (has_flash('error')): ?>
    <div class="alert alert-danger mb-3 py-2 px-3 d-flex align-items-center gap-2 text-sm rounded-3">
        <i class="bi bi-exclamation-circle-fill text-danger flex-shrink-0"></i>
        <div><?= e(get_flash('error')) ?></div>
    </div>
    <?php endif; ?>

    <?php if (has_flash('success')): ?>
    <div class="alert alert-success mb-3 py-2 px-3 d-flex align-items-center gap-2 text-sm rounded-3">
        <i class="bi bi-check-circle-fill text-success flex-shrink-0"></i>
        <div><?= e(get_flash('success')) ?></div>
    </div>
    <?php endif; ?>

    <!-- Admin Login Form -->
    <form method="POST" action="<?= url('admin/login') ?>" id="adminLoginForm" autocomplete="on">
        <?= csrf_field() ?>

        <!-- Email Field -->
        <div class="mb-3">
            <label for="adminEmail" class="form-label fw-700 text-dark small mb-1">Administrator Email</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1;">
                    <i class="bi bi-envelope"></i>
                </span>
                <input 
                    type="email" 
                    id="adminEmail" 
                    name="email" 
                    class="form-control form-control-lg border-start-0 text-dark fw-600 <?= has_error('email') ? 'is-invalid' : '' ?>" 
                    value="<?= e(old('email')) ?>"
                    placeholder="admin@example.com"
                    autocomplete="email"
                    required 
                    autofocus
                    style="font-size: 0.95rem;"
                >
            </div>
            <?php if (has_error('email')): ?>
            <div class="text-danger text-xs mt-1 fw-600"><?= e(error('email')) ?></div>
            <?php endif; ?>
        </div>

        <!-- Password Field -->
        <div class="mb-4">
            <label for="adminPassword" class="form-label fw-700 text-dark small mb-1">Security Password</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1;">
                    <i class="bi bi-lock"></i>
                </span>
                <input 
                    type="password" 
                    id="adminPassword" 
                    name="password" 
                    class="form-control form-control-lg border-start-0 border-end-0 text-dark fw-600 <?= has_error('password') ? 'is-invalid' : '' ?>" 
                    placeholder="Enter security password"
                    autocomplete="current-password"
                    required
                    style="font-size: 0.95rem;"
                >
                <button class="btn btn-outline-secondary border-start-0 bg-white" type="button" onclick="togglePasswordVisibility()" style="border-color: #CBD5E1; color: #64748B;">
                    <i class="bi bi-eye" id="toggleEyeIcon"></i>
                </button>
            </div>
            <?php if (has_error('password')): ?>
            <div class="text-danger text-xs mt-1 fw-600"><?= e(error('password')) ?></div>
            <?php endif; ?>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary-gradient btn-lg w-100 py-3 fw-800 shadow" id="submitBtn">
            <i class="bi bi-shield-check me-2"></i> Sign In to Admin Panel
        </button>
    </form>

    <!-- Links Footer -->
    <div class="text-center mt-4 border-top pt-3">
        <a href="<?= url('login') ?>" class="text-secondary small fw-600 text-decoration-none hover-primary">
            <i class="bi bi-shop me-1"></i> Switch to Merchant Login
        </a>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    var passInput = document.getElementById('adminPassword');
    var icon = document.getElementById('toggleEyeIcon');
    if (passInput.type === 'password') {
        passInput.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        passInput.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>

<?php View::endSection(); ?>
