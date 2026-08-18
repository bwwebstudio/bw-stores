<?php

use App\Core\View;
View::layout('layouts.auth');

View::section('title'); ?>Password Assistance — BW Store<?php View::endSection();

View::section('content'); ?>

<div class="auth-card">
    <div class="auth-header text-center mb-4">
        <div class="logo-mark mx-auto mb-3">
            <i class="bi bi-key-fill text-white fs-4"></i>
        </div>
        <h2 class="fw-900 text-dark mb-1">Reset Password</h2>
        <p class="text-secondary small mb-0 fw-500">Enter your registered email to receive reset instructions</p>
    </div>

    <?php if ($generalError = error('general')): ?>
    <div class="alert alert-danger mb-3 py-2 px-3 d-flex align-items-center gap-2 text-sm rounded-3">
        <i class="bi bi-exclamation-triangle-fill text-danger flex-shrink-0"></i>
        <div><?= e($generalError) ?></div>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('forgot-password') ?>" id="forgotPasswordForm">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="email" class="form-label fw-700 text-dark small mb-1">Registered Email Address</label>
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

        <button type="submit" class="btn btn-primary-gradient btn-lg w-100 py-3 fw-800 shadow mb-3">
            <i class="bi bi-send-fill me-2"></i> Send Password Reset Link
        </button>
    </form>

    <div class="auth-card-footer mt-4 pt-3 border-top text-center">
        <a href="<?= url('login') ?>" class="text-secondary small fw-700 text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Return to Merchant Login
        </a>
    </div>
</div>

<?php View::endSection(); ?>
