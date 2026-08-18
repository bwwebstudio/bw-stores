<?php

use App\Core\View;
View::layout('layouts.auth');

View::section('title'); ?>Set New Password — BW Store<?php View::endSection();

View::section('content'); ?>

<div class="auth-card">
    <div class="auth-header text-center mb-4">
        <div class="logo-mark mx-auto mb-3">
            <i class="bi bi-shield-check text-white fs-4"></i>
        </div>
        <h2 class="fw-900 text-dark mb-1">Set New Password</h2>
        <p class="text-secondary small mb-0 fw-500">Create a secure password for your store account</p>
    </div>

    <?php if ($generalError = error('general')): ?>
    <div class="alert alert-danger mb-3 py-2 px-3 d-flex align-items-center gap-2 text-sm rounded-3">
        <i class="bi bi-exclamation-triangle-fill text-danger flex-shrink-0"></i>
        <div><?= e($generalError) ?></div>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('reset-password') ?>" id="resetPasswordForm">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token ?? '') ?>">

        <div class="mb-3">
            <label for="password" class="form-label fw-700 text-dark small mb-1">New Password</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1;">
                    <i class="bi bi-lock"></i>
                </span>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control form-control-lg border-start-0 text-dark fw-600 <?= has_error('password') ? 'is-invalid' : '' ?>" 
                    placeholder="Min 8 characters"
                    autocomplete="new-password"
                    required 
                    autofocus
                    style="font-size: 0.95rem;"
                >
            </div>
            <?php if (has_error('password')): ?>
            <div class="text-danger text-xs mt-1 fw-600"><?= e(error('password')) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-700 text-dark small mb-1">Confirm New Password</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1;">
                    <i class="bi bi-shield-lock"></i>
                </span>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="form-control form-control-lg border-start-0 text-dark fw-600 <?= has_error('password_confirmation') ? 'is-invalid' : '' ?>"
                    placeholder="Confirm new password"
                    autocomplete="new-password"
                    required
                    style="font-size: 0.95rem;"
                >
            </div>
            <?php if (has_error('password_confirmation')): ?>
            <div class="text-danger text-xs mt-1 fw-600"><?= e(error('password_confirmation')) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary-gradient btn-lg w-100 py-3 fw-800 shadow mb-3">
            <i class="bi bi-check-circle-fill me-2"></i> Update Password & Sign In
        </button>
    </form>

    <div class="auth-card-footer mt-4 pt-3 border-top text-center">
        <a href="<?= url('login') ?>" class="text-secondary small fw-700 text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Back to Login
        </a>
    </div>
</div>

<?php View::endSection(); ?>
