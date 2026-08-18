<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Orders<?php View::endSection();
View::section('page_title'); ?>Customer Orders<?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-700 mb-1">Orders (<?= $total ?>)</h4>
        <p class="text-muted small mb-0">Track and fulfill customer orders placed across your storefront.</p>
    </div>
</div>

<!-- Filters Bar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= url('dashboard/orders') ?>" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search order #, customer, or phone..." value="<?= e($filters['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="order_status" class="form-select form-select-sm">
                    <option value="">All Order Statuses</option>
                    <option value="PENDING" <?= ($filters['order_status'] ?? '') === 'PENDING' ? 'selected' : '' ?>>Pending</option>
                    <option value="CONFIRMED" <?= ($filters['order_status'] ?? '') === 'CONFIRMED' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="PROCESSING" <?= ($filters['order_status'] ?? '') === 'PROCESSING' ? 'selected' : '' ?>>Processing</option>
                    <option value="SHIPPED" <?= ($filters['order_status'] ?? '') === 'SHIPPED' ? 'selected' : '' ?>>Shipped</option>
                    <option value="DELIVERED" <?= ($filters['order_status'] ?? '') === 'DELIVERED' ? 'selected' : '' ?>>Delivered</option>
                    <option value="CANCELLED" <?= ($filters['order_status'] ?? '') === 'CANCELLED' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="payment_status" class="form-select form-select-sm">
                    <option value="">All Payments</option>
                    <option value="PAID" <?= ($filters['payment_status'] ?? '') === 'PAID' ? 'selected' : '' ?>>Paid</option>
                    <option value="PENDING" <?= ($filters['payment_status'] ?? '') === 'PENDING' ? 'selected' : '' ?>>Pending</option>
                    <option value="FAILED" <?= ($filters['payment_status'] ?? '') === 'FAILED' ? 'selected' : '' ?>>Failed</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-dark w-100">Filter</button>
                <a href="<?= url('dashboard/orders') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                <h5 class="fw-700 mt-3">No orders found</h5>
                <p class="text-muted small">Orders placed on your storefront will automatically appear here.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>
                                <a href="<?= url('dashboard/orders/' . $o['id']) ?>" class="fw-700 text-primary">
                                    #<?= e($o['order_number']) ?>
                                </a>
                            </td>
                            <td class="text-muted text-xs">
                                <?= date('M d, Y H:i', strtotime($o['created_at'])) ?>
                            </td>
                            <td>
                                <div class="fw-600 text-dark"><?= e($o['customer_name']) ?></div>
                                <div class="text-muted text-xs"><?= e($o['customer_mobile']) ?></div>
                            </td>
                            <td><?= $o['total_items'] ?> items</td>
                            <td>
                                <span class="fw-700 text-dark">₹<?= number_format($o['total'], 2) ?></span>
                            </td>
                            <td>
                                <?php if ($o['payment_status'] === 'PAID'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">PAID (<?= e($o['payment_method']) ?>)</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">PENDING (<?= e($o['payment_method']) ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $badgeClass = match($o['order_status']) {
                                        'DELIVERED' => 'badge-success',
                                        'SHIPPED'   => 'badge-info',
                                        'PROCESSING', 'CONFIRMED' => 'badge-warning',
                                        'CANCELLED' => 'badge-danger',
                                        default     => 'badge-gray',
                                    };
                                ?>
                                <span class="badge-status <?= $badgeClass ?>"><?= $o['order_status'] ?></span>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('dashboard/orders/' . $o['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="<?= url('dashboard/orders/' . $o['id'] . '/invoice') ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php View::endSection(); ?>
