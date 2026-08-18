<?php

use App\Core\View;
View::layout('layouts.auth');

View::section('title'); ?>Merchant Login — BW Store<?php View::endSection();

View::section('content'); ?>

<div class="auth-card">
    <div class="auth-header text-center mb-4">
        <div class="logo-mark mx-auto mb-3">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 7H20L18.5 21H5.5L4 7Z" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8.5 7V4.5C8.5 3.11929 9.61929 2 11 2H13C14.3807 2 15.5 3.11929 15.5 4.5V7" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                <path d="M9 13L11.5 15.5L15.5 10.5" stroke="#60A5FA" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h2 class="fw-900 text-dark mb-1">Welcome back</h2>
        <p class="text-secondary small mb-0 fw-500">Sign in to manage your BW Store and sales</p>
    </div>

    <?php if ($generalError = error('general')): ?>
    <div class="alert alert-danger mb-3 py-2 px-3 d-flex align-items-center gap-2 text-sm rounded-3">
        <i class="bi bi-exclamation-triangle-fill text-danger flex-shrink-0"></i>
        <div><?= e($generalError) ?></div>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('login') ?>" id="merchantLoginForm">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="email" class="form-label fw-700 text-dark small mb-1">Email address</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1;">
                    <i class="bi bi-envelope"></i>
                </span>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control form-control-lg border-start-0 text-dark fw-600 <?= has_error('email') ? 'is-invalid' : '' ?>" 
                    value="<?= e(old('email')) ?>"
                    placeholder="merchant@example.com"
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

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label fw-700 text-dark small mb-0">Password</label>
                <a href="<?= url('forgot-password') ?>" class="text-primary text-xs fw-700 text-decoration-none">Forgot password?</a>
            </div>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1;">
                    <i class="bi bi-lock"></i>
                </span>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control form-control-lg border-start-0 border-end-0 text-dark fw-600 <?= has_error('password') ? 'is-invalid' : '' ?>" 
                    placeholder="Enter your password"
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

        <button type="submit" class="btn btn-primary-gradient btn-lg w-100 py-3 fw-800 shadow">
            <i class="bi bi-box-arrow-in-right me-2"></i> Log In to Store Dashboard
        </button>
    </form>

    <div class="auth-card-footer mt-4 pt-3 border-top text-center">
        <p class="text-secondary small mb-2">
            Don't have an online store yet? <a href="<?= url('signup') ?>" class="text-primary fw-800">Start 7-Day Free Trial</a>
        </p>
        <p class="text-muted text-xs mb-0">
            Platform Administrator? <a href="<?= url('admin/login') ?>" class="text-dark fw-700">Admin Portal &rarr;</a>
        </p>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    var passInput = document.getElementById('password');
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
