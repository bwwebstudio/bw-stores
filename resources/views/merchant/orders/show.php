<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Order #<?= e($order['order_number']) ?><?php View::endSection();
View::section('page_title'); ?>Order Details: #<?= e($order['order_number']) ?><?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <a href="<?= url('dashboard/orders') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Orders
    </a>
    <div class="d-flex gap-2">
        <a href="<?= url('dashboard/orders/' . $order['id'] . '/invoice') ?>" target="_blank" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-printer me-1"></i> Print Invoice
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left 8 Columns: Items & Financial Breakdown -->
    <div class="col-lg-8">
        <!-- Order Items -->
        <div class="card mb-4">
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <h5 class="fw-700 mb-0">Purchased Items (<?= count($order['items']) ?>)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Product Details</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td>
                                    <div class="fw-600 text-dark"><?= e($item['product_name']) ?></div>
                                    <?php if (!empty($item['variant_name'])): ?>
                                        <div class="badge bg-light text-secondary border"><?= e($item['variant_name']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['sku'])): ?>
                                        <span class="text-muted text-xs ms-1">SKU: <?= e($item['sku']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>₹<?= number_format($item['price'], 2) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td class="text-end fw-700">₹<?= number_format($item['total'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="fw-700 mb-3">Order Financial Summary</h5>
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-600">₹<?= number_format($order['subtotal'], 2) ?></span>
                        </div>
                        <?php if ($order['discount'] > 0): ?>
                        <div class="d-flex justify-content-between py-1 border-bottom text-success">
                            <span>Discount (<?= e($order['coupon_code'] ?: 'Promo') ?>):</span>
                            <span>-₹<?= number_format($order['discount'], 2) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Shipping Fee:</span>
                            <span><?= $order['shipping'] > 0 ? '₹' . number_format($order['shipping'], 2) : 'Free Shipping' ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 fs-5 fw-800 text-dark">
                            <span>Final Total:</span>
                            <span class="text-primary">₹<?= number_format($order['total'], 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right 4 Columns: Customer & Status Updates -->
    <div class="col-lg-4">
        <!-- Status Updater -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="fw-700 mb-3">Update Order Status</h5>
                <form method="POST" action="<?= url('dashboard/orders/' . $order['id'] . '/status') ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Fulfillment Status</label>
                        <select name="order_status" class="form-select">
                            <?php
                            $statuses = ['PENDING', 'CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'CANCELLED', 'RETURNED', 'REFUNDED'];
                            foreach ($statuses as $st): ?>
                                <option value="<?= $st ?>" <?= $order['order_status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-select">
                            <option value="PAID" <?= $order['payment_status'] === 'PAID' ? 'selected' : '' ?>>PAID</option>
                            <option value="PENDING" <?= $order['payment_status'] === 'PENDING' ? 'selected' : '' ?>>PENDING</option>
                            <option value="FAILED" <?= $order['payment_status'] === 'FAILED' ? 'selected' : '' ?>>FAILED</option>
                            <option value="REFUNDED" <?= $order['payment_status'] === 'REFUNDED' ? 'selected' : '' ?>>REFUNDED</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-600">
                        <i class="bi bi-check2-circle me-1"></i> Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Customer & Shipping -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="fw-700 mb-3">Customer & Shipping</h5>
                <div class="mb-3">
                    <div class="text-muted text-xs uppercase">Customer Name</div>
                    <div class="fw-700 text-dark"><?= e($order['customer_name']) ?></div>
                </div>
                <div class="mb-3">
                    <div class="text-muted text-xs uppercase">Contact Info</div>
                    <div><i class="bi bi-envelope text-muted me-1"></i> <?= e($order['customer_email']) ?></div>
                    <div><i class="bi bi-telephone text-muted me-1"></i> <?= e($order['customer_mobile']) ?></div>
                </div>
                <div class="mb-3">
                    <div class="text-muted text-xs uppercase">Delivery Address</div>
                    <div class="text-dark small">
                        <?= nl2br(e($order['shipping_address'])) ?><br>
                        <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> - <?= e($order['shipping_postal_code']) ?>
                    </div>
                </div>
                <div>
                    <div class="text-muted text-xs uppercase">Payment Method</div>
                    <div class="badge bg-light text-dark border"><?= e($order['payment_method']) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php View::endSection(); ?>
