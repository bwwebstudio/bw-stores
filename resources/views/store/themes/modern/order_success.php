<?php

use App\Core\View;
View::layout('layouts.storefront');

View::section('title'); ?>Order Confirmed! — <?= e($store['name']) ?><?php View::endSection();

View::section('content'); ?>

<div class="container py-5 text-center" style="max-width: 680px;">
    <div class="card p-5 border-0 shadow-sm rounded-4">
        <div class="rounded-circle bg-success text-white mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2.25rem;">
            <i class="bi bi-check-lg"></i>
        </div>

        <h2 class="fw-900 text-dark mb-1">Thank You For Your Order!</h2>
        <p class="text-muted mb-4">We've received your order and are preparing it for shipment.</p>

        <div class="bg-light rounded-3 p-4 text-start mb-4">
            <div class="d-flex justify-content-between border-bottom pb-2 mb-3">
                <span class="text-muted small">Order Reference Number:</span>
                <strong class="text-primary fs-6">#<?= e($order['order_number']) ?></strong>
            </div>

            <div class="row g-3 small">
                <div class="col-6">
                    <span class="text-muted d-block">Recipient:</span>
                    <strong><?= e($order['customer_name']) ?></strong>
                    <div class="text-secondary"><?= e($order['customer_mobile']) ?></div>
                </div>
                <div class="col-6 text-end">
                    <span class="text-muted d-block">Payment Method:</span>
                    <strong><?= e($order['payment_method']) ?></strong>
                    <div class="text-success"><?= e($order['payment_status']) ?></div>
                </div>
                <div class="col-12 border-top pt-2">
                    <span class="text-muted d-block">Shipping Address:</span>
                    <div><?= nl2br(e($order['shipping_address'])) ?>, <?= e($order['shipping_city']) ?> - <?= e($order['shipping_postal_code']) ?></div>
                </div>
            </div>

            <div class="border-top pt-3 mt-3 d-flex justify-content-between fs-5 fw-800 text-dark">
                <span>Total Amount:</span>
                <span class="text-store-primary">₹<?= number_format($order['total'], 2) ?></span>
            </div>
        </div>

        <div class="d-flex justify-content-center gap-3">
            <a href="<?= url('store/' . $store['slug']) ?>" class="btn btn-store-primary px-4 py-2">
                <i class="bi bi-arrow-left me-1"></i> Continue Shopping
            </a>
        </div>
    </div>
</div>

<?php View::endSection(); ?>
