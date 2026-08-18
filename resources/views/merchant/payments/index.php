<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Payment Settings<?php View::endSection();
View::section('page_title'); ?>Customer Payment Gateways & UPI<?php View::endSection();

View::section('content'); ?>

<form method="POST" action="<?= url('dashboard/payments') ?>" autocomplete="off" data-loading>
    <?= csrf_field() ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- 1. Direct UPI QR & VPA Settings -->
            <div class="card mb-4 border shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-info text-white d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-weight: 800; font-size: 1.25rem;">
                                UPI
                            </div>
                            <div>
                                <h5 class="fw-800 text-dark mb-0">Merchant Direct UPI & QR Payments</h5>
                                <span class="text-muted small">Customers pay directly to your Google Pay, PhonePe, Paytm or BHIM UPI</span>
                            </div>
                        </div>

                        <div class="form-check form-switch fs-4">
                            <input class="form-check-input cursor-pointer" type="checkbox" name="upi_enabled" value="1" id="upiToggle" <?= (!isset($settings['upi_enabled']) || $settings['upi_enabled']) ? 'checked' : '' ?>>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-600 text-dark">Your Merchant UPI ID / VPA</label>
                            <input type="text" name="merchant_upi_id" class="form-control font-monospace fw-700" placeholder="e.g. yourbrand@okhdfcbank" value="<?= e($settings['merchant_upi_id'] ?? '') ?>" autocomplete="off">
                            <div class="text-muted text-xs mt-1">Found in your Google Pay / PhonePe / Paytm profile.</div>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-600 text-dark">Payee / Account Name</label>
                            <input type="text" name="merchant_upi_name" class="form-control" placeholder="e.g. Burhan Collections" value="<?= e($settings['merchant_upi_name'] ?? ($store['name'] ?? '')) ?>" autocomplete="off">
                            <div class="text-muted text-xs mt-1">Name shown on UPI confirmation.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Razorpay Gateway Connect -->
            <div class="card mb-4 border shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-weight: 800; font-size: 1.25rem;">
                                RZP
                            </div>
                            <div>
                                <h5 class="fw-800 text-dark mb-0">Merchant Razorpay Account (Cards & NetBanking)</h5>
                                <span class="text-muted small">Accept Credit/Debit Cards, NetBanking, and Wallets</span>
                            </div>
                        </div>

                        <?php if (!empty($settings['razorpay_connected'])): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                                <i class="bi bi-check-circle-fill me-1"></i> CONNECTED
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary border px-3 py-2 fs-6">
                                <i class="bi bi-x-circle me-1"></i> NOT CONNECTED
                            </span>
                        <?php endif; ?>
                    </div>

                    <hr class="my-3">

                    <div class="mb-3">
                        <label class="form-label fw-600 text-dark">Merchant Razorpay Key ID</label>
                        <input type="text" name="razorpay_key_id" class="form-control font-monospace" placeholder="rzp_live_xxxxxxxxxxxxxx" value="<?= e($settings['razorpay_key_id'] ?? '') ?>" autocomplete="off">
                        <div class="text-muted text-xs mt-1">From your Razorpay Dashboard &rarr; Settings &rarr; API Keys.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600 text-dark">Merchant Razorpay Key Secret</label>
                        <input type="password" name="razorpay_key_secret" class="form-control" placeholder="••••••••••••••••••••••••" value="<?= e($settings['razorpay_key_secret'] ?? '') ?>" autocomplete="new-password">
                        <div class="text-muted text-xs mt-1">Encrypted & securely stored in your database.</div>
                    </div>
                </div>
            </div>

            <!-- 3. Cash on Delivery (COD) -->
            <div class="card mb-4 border shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 bg-success text-white d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-weight: 800; font-size: 1.25rem;">
                                COD
                            </div>
                            <div>
                                <h5 class="fw-800 text-dark mb-0">Cash on Delivery (COD)</h5>
                                <span class="text-muted small">Allow customers to pay in cash when delivery partner delivers the parcel</span>
                            </div>
                        </div>

                        <div class="form-check form-switch fs-4">
                            <input class="form-check-input cursor-pointer" type="checkbox" name="cod_enabled" value="1" id="codToggle" <?= (!isset($settings['cod_enabled']) || $settings['cod_enabled']) ? 'checked' : '' ?>>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg px-5 py-3 fw-800 shadow">
                <i class="bi bi-check2-circle me-1"></i> Save Store Payment Settings
            </button>
        </div>

        <!-- Security Information Panel -->
        <div class="col-lg-4">
            <div class="card bg-light border mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-800 text-dark mb-2"><i class="bi bi-shield-check text-success me-2"></i>Direct 0% Commission</h6>
                    <p class="text-secondary small mb-3">
                        When customers order on your storefront, 100% of the payment goes straight to your UPI ID or Razorpay account.
                    </p>
                    <div class="p-3 bg-white rounded-3 border">
                        <div class="fw-700 text-success small mb-1"><i class="bi bi-check-circle-fill me-1"></i> 0% Platform Commission</div>
                        <div class="text-muted text-xs">You keep every rupee of your customer sales.</div>
                    </div>
                </div>
            </div>

            <?php if (!empty($settings['merchant_upi_id'])): ?>
            <!-- Live QR Preview for Merchant -->
            <div class="card border p-3 text-center bg-white shadow-sm">
                <span class="badge bg-info-subtle text-info border mb-2 text-xs fw-700">STOREFRONT QR PREVIEW</span>
                <h6 class="fw-700 text-dark mb-2">Customer Payment QR</h6>
                <div class="p-2 bg-light rounded border mx-auto mb-2" style="width: 140px; height: 140px;">
                    <?php
                        $demoUpi = "upi://pay?pa=" . urlencode($settings['merchant_upi_id']) . "&pn=" . urlencode($settings['merchant_upi_name'] ?? $store['name']) . "&cu=INR";
                        $demoQr = "https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=" . urlencode($demoUpi);
                    ?>
                    <img src="<?= $demoQr ?>" alt="UPI QR" class="img-fluid rounded" style="width: 100%; height: 100%;">
                </div>
                <code class="fw-700 text-dark small"><?= e($settings['merchant_upi_id']) ?></code>
            </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php View::endSection(); ?>
