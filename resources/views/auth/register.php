<?php

use App\Core\View;
View::layout('layouts.auth');

View::section('title'); ?>Create Store Account — 7-Day Free Trial<?php View::endSection();

View::section('content'); ?>

<div class="auth-card" style="max-width: 520px;">
    <div class="auth-header text-center mb-4">
        <div class="logo-mark mx-auto mb-3">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 7H20L18.5 21H5.5L4 7Z" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8.5 7V4.5C8.5 3.11929 9.61929 2 11 2H13C14.3807 2 15.5 3.11929 15.5 4.5V7" stroke="white" stroke-width="2.2" stroke-linecap="round"/>
                <path d="M9 13L11.5 15.5L15.5 10.5" stroke="#60A5FA" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h2 class="fw-900 text-dark mb-1">Create your store</h2>
        <div class="d-inline-flex align-items-center gap-1 badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 mt-1 mb-2 fw-700">
            <i class="bi bi-gift-fill text-warning me-1"></i> 7-Day Free Trial Included &bull; 0% Cut
        </div>
        <p class="text-secondary small mb-0 fw-500">Launch your independent online brand in under 5 minutes</p>
    </div>

    <?php if ($generalError = error('general')): ?>
    <div class="alert alert-danger mb-3 py-2 px-3 d-flex align-items-center gap-2 text-sm rounded-3">
        <i class="bi bi-exclamation-triangle-fill text-danger flex-shrink-0"></i>
        <div><?= e($generalError) ?></div>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('signup') ?>" id="merchantRegisterForm">
        <?= csrf_field() ?>

        <!-- Full Name -->
        <div class="mb-3">
            <label for="name" class="form-label fw-700 text-dark small mb-1">Full Name</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1;">
                    <i class="bi bi-person"></i>
                </span>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control form-control-lg border-start-0 text-dark fw-600 <?= has_error('name') ? 'is-invalid' : '' ?>" 
                    value="<?= e(old('name')) ?>"
                    placeholder="Enter your name"
                    autocomplete="name"
                    required 
                    autofocus
                    style="font-size: 0.95rem;"
                >
            </div>
            <?php if (has_error('name')): ?>
            <div class="text-danger text-xs mt-1 fw-600"><?= e(error('name')) ?></div>
            <?php endif; ?>
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-700 text-dark small mb-1">Email Address</label>
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
                    style="font-size: 0.95rem;"
                >
            </div>
            <?php if (has_error('email')): ?>
            <div class="text-danger text-xs mt-1 fw-600"><?= e(error('email')) ?></div>
            <?php endif; ?>
        </div>

        <!-- Mobile Number -->
        <div class="mb-3">
            <label for="mobile" class="form-label fw-700 text-dark small mb-1">Mobile / WhatsApp Number <span class="text-muted fw-400">(Optional)</span></label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1;">
                    <i class="bi bi-phone"></i>
                </span>
                <input 
                    type="tel" 
                    id="mobile" 
                    name="mobile" 
                    class="form-control form-control-lg border-start-0 text-dark fw-600 <?= has_error('mobile') ? 'is-invalid' : '' ?>" 
                    value="<?= e(old('mobile')) ?>"
                    placeholder="+91 98765 43210"
                    autocomplete="tel"
                    style="font-size: 0.95rem;"
                >
            </div>
            <?php if (has_error('mobile')): ?>
            <div class="text-danger text-xs mt-1 fw-600"><?= e(error('mobile')) ?></div>
            <?php endif; ?>
        </div>

        <!-- Password Fields (2 Column Grid) -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="password" class="form-label fw-700 text-dark small mb-1">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1;">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control form-control-lg border-start-0 text-dark fw-600 <?= has_error('password') ? 'is-invalid' : '' ?>" 
                        placeholder="Min 8 chars"
                        autocomplete="new-password"
                        required
                        style="font-size: 0.95rem;"
                    >
                </div>
                <?php if (has_error('password')): ?>
                <div class="text-danger text-xs mt-1 fw-600"><?= e(error('password')) ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label for="password_confirmation" class="form-label fw-700 text-dark small mb-1">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-color: #CBD5E1;">
                        <i class="bi bi-shield-check"></i>
                    </span>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="form-control form-control-lg border-start-0 text-dark fw-600 <?= has_error('password_confirmation') ? 'is-invalid' : '' ?>"
                        placeholder="Confirm password"
                        autocomplete="new-password"
                        required
                        style="font-size: 0.95rem;"
                    >
                </div>
                <?php if (has_error('password_confirmation')): ?>
                <div class="text-danger text-xs mt-1 fw-600"><?= e(error('password_confirmation')) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary-gradient btn-lg w-100 py-3 fw-800 shadow">
            <i class="bi bi-rocket-takeoff-fill me-2"></i> Start 7-Day Free Trial Now
        </button>

        <p class="text-muted text-xs text-center mt-3 mb-0" style="line-height: 1.5;">
            By signing up, you agree to our Terms of Service. No credit card required to start trial.
        </p>
    </form>

    <div class="auth-card-footer mt-4 pt-3 border-top text-center">
        Already have a store account? <a href="<?= url('login') ?>" class="text-primary fw-800">Log In</a>
    </div>
</div>

<?php View::endSection(); ?>
