<?php

use App\Core\View;
View::layout('layouts.storefront');

View::section('title'); ?>Checkout — <?= e($store['name']) ?><?php View::endSection();

View::section('content'); ?>

<div class="bg-light py-4 border-bottom mb-4">
    <div class="container">
        <h2 class="fw-800 text-dark mb-0">Secure Checkout</h2>
    </div>
</div>

<div class="container mb-5">
    <form method="POST" action="<?= url('store/' . $store['slug'] . '/checkout') ?>" data-loading>
        <?= csrf_field() ?>

        <div class="row g-5">
            <!-- Left 7 Columns: Customer Shipping Information -->
            <div class="col-lg-7">
                <div class="card p-4 mb-4">
                    <h5 class="fw-700 mb-3"><i class="bi bi-person text-primary me-2"></i>1. Customer Information</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" placeholder="John Doe" value="<?= e(old('customer_name')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="customer_email" class="form-control" placeholder="john@example.com" value="<?= e(old('customer_email')) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Mobile / Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="customer_mobile" class="form-control" placeholder="+91 98765 43210" value="<?= e(old('customer_mobile')) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="card p-4 mb-4">
                    <h5 class="fw-700 mb-3"><i class="bi bi-truck text-primary me-2"></i>2. Shipping Address</h5>

                    <div class="mb-3">
                        <label class="form-label">Street Address & Landmark <span class="text-danger">*</span></label>
                        <textarea name="shipping_address" rows="2" class="form-control" placeholder="Flat / House No, Building, Street, Area..." required><?= e(old('shipping_address')) ?></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_city" class="form-control" placeholder="Mumbai" value="<?= e(old('shipping_city')) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_state" class="form-control" placeholder="Maharashtra" value="<?= e(old('shipping_state')) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">PIN / Postal Code <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_postal_code" class="form-control" placeholder="400001" value="<?= e(old('shipping_postal_code')) ?>" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Delivery Instructions (Optional)</label>
                        <input type="text" name="notes" class="form-control" placeholder="Leave with security, call on arrival, etc.">
                    </div>
                </div>

                <div class="card p-4">
                    <h5 class="fw-700 mb-3"><i class="bi bi-credit-card text-primary me-2"></i>3. Payment Method</h5>

                    <?php 
                    $isCod = (!isset($settings['cod_enabled']) || $settings['cod_enabled']);
                    $isUpi = (!empty($settings['merchant_upi_id']) && (!isset($settings['upi_enabled']) || $settings['upi_enabled']));
                    $isOnline = !empty($settings['razorpay_connected']);
                    ?>

                    <!-- Option 1: Direct Merchant UPI QR -->
                    <?php if ($isUpi): ?>
                    <div class="form-check p-3 border rounded mb-2 <?= (!$isCod) ? 'bg-light' : '' ?>">
                        <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" value="UPI" id="pay_upi" <?= (!$isCod) ? 'checked' : '' ?> onclick="toggleUpiBox(true)">
                        <label class="form-check-label fw-700 text-dark" for="pay_upi">
                            <i class="bi bi-qr-code text-info me-1"></i> Direct UPI & QR Code (GPay / PhonePe / Paytm)
                            <div class="text-muted text-xs fw-normal">Pay directly to <strong><?= e($settings['merchant_upi_id']) ?></strong> via your UPI app</div>
                        </label>

                        <!-- UPI QR Box -->
                        <div id="customerUpiBox" class="mt-3 p-3 bg-white rounded border text-center <?= ($isCod) ? 'd-none' : '' ?>">
                            <p class="small text-muted mb-2">Scan with Google Pay, PhonePe, or Paytm to pay <strong>₹<?= number_format($total, 2) ?></strong>:</p>
                            <?php if (!empty($customerQrUrl)): ?>
                            <div class="mx-auto p-2 bg-light rounded border mb-2" style="width: 170px; height: 170px;">
                                <img src="<?= $customerQrUrl ?>" alt="Pay with UPI" class="img-fluid" style="width: 100%; height: 100%;">
                            </div>
                            <?php endif; ?>
                            <div class="d-md-none mb-2">
                                <a href="<?= $customerUpiLink ?>" class="btn btn-outline-success btn-sm w-100 fw-600">
                                    <i class="bi bi-phone me-1"></i> Open Google Pay / PhonePe App
                                </a>
                            </div>
                            <div class="text-start mt-2">
                                <label class="form-label text-xs fw-600 text-dark">Enter 12-Digit UPI Transaction UTR / Ref Number <span class="text-danger">*</span></label>
                                <input type="text" name="customer_utr" class="form-control form-control-sm text-center font-monospace" placeholder="e.g. 423871928371">
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Option 2: Razorpay Online -->
                    <?php if ($isOnline): ?>
                    <div class="form-check p-3 border rounded mb-2 <?= (!$isCod && !$isUpi) ? 'bg-light' : '' ?>">
                        <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" value="ONLINE" id="pay_online" <?= (!$isCod && !$isUpi) ? 'checked' : '' ?> onclick="toggleUpiBox(false)">
                        <label class="form-check-label fw-700 text-dark" for="pay_online">
                            <i class="bi bi-credit-card-2-front text-primary me-1"></i> Instant Online Payment (Cards / NetBanking)
                            <div class="text-muted text-xs fw-normal">Secured via Merchant Razorpay Gateway</div>
                        </label>
                    </div>
                    <?php endif; ?>

                    <!-- Option 3: Cash on Delivery -->
                    <?php if ($isCod): ?>
                    <div class="form-check p-3 border rounded mb-2 bg-light">
                        <input class="form-check-input ms-0 me-2" type="radio" name="payment_method" value="COD" id="pay_cod" checked onclick="toggleUpiBox(false)">
                        <label class="form-check-label fw-700 text-dark" for="pay_cod">
                            <i class="bi bi-cash-coin text-success me-1"></i> Cash on Delivery (COD)
                            <div class="text-muted text-xs fw-normal">Pay cash or UPI to delivery agent when parcel arrives</div>
                        </label>
                    </div>
                    <?php endif; ?>

                    <?php if (!$isCod && !$isOnline && !$isUpi): ?>
                    <div class="alert alert-warning small mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i> Store is currently not accepting new payments. Please contact merchant.
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <script>
            function toggleUpiBox(show) {
                var box = document.getElementById('customerUpiBox');
                if (box) {
                    if (show) box.classList.remove('d-none');
                    else box.classList.add('d-none');
                }
            }
            </script>

            <!-- Right 5 Columns: Order Summary Breakdown -->
            <div class="col-lg-5">
                <div class="card p-4">
                    <h5 class="fw-700 mb-3">Your Order (<?= count($cart) ?>)</h5>

                    <div class="list-group list-group-flush mb-3">
                        <?php foreach ($cart as $item): ?>
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-600 text-dark small"><?= e($item['name']) ?></div>
                                <div class="text-muted text-xs">Qty: <?= $item['quantity'] ?> <?= !empty($item['variant_name']) ? ' &bull; ' . e($item['variant_name']) : '' ?></div>
                            </div>
                            <span class="fw-700 small">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between py-1 small">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-600">₹<?= number_format($subtotal, 2) ?></span>
                    </div>

                    <?php if ($discount > 0): ?>
                    <div class="d-flex justify-content-between py-1 small text-success">
                        <span>Coupon (<?= e($appliedCoupon['code']) ?>):</span>
                        <span>-₹<?= number_format($discount, 2) ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between py-1 small">
                        <span class="text-muted">Shipping:</span>
                        <span class="text-success fw-600">FREE</span>
                    </div>

                    <div class="d-flex justify-content-between py-3 fs-5 fw-800 text-dark border-top mt-2">
                        <span>Grand Total:</span>
                        <span class="text-store-primary">₹<?= number_format($total, 2) ?></span>
                    </div>

                    <button type="submit" class="btn btn-store-primary btn-lg w-100 py-3 fw-700 shadow">
                        <i class="bi bi-shield-check me-1"></i> Place Order Now
                    </button>
                    <div class="text-center text-muted text-xs mt-2">
                        <i class="bi bi-lock me-1"></i> 256-bit encrypted secure checkout
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php View::endSection(); ?>
