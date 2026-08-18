<?php

use App\Core\View;
View::layout('layouts.admin');

View::section('title'); ?>Global Orders<?php View::endSection();
View::section('page_title'); ?>Global Orders Activity<?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-700 mb-1">Global Customer Orders (<?= $total ?>)</h4>
        <p class="text-muted small mb-0">Platform-wide monitor of all customer orders placed on merchant storefronts.</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-receipt" style="font-size: 3rem;"></i>
                <p class="mt-2 mb-0">No customer orders placed yet across the platform.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Store</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><strong class="text-primary">#<?= e($o['order_number']) ?></strong></td>
                            <td>
                                <div class="fw-600 text-dark"><?= e($o['store_name']) ?></div>
                                <div class="text-muted text-xs"><?= e($o['business_name']) ?></div>
                            </td>
                            <td>
                                <div><?= e($o['customer_name']) ?></div>
                                <div class="text-muted text-xs"><?= e($o['customer_mobile']) ?></div>
                            </td>
                            <td class="fw-700">₹<?= number_format($o['total'], 2) ?></td>
                            <td>
                                <span class="badge bg-<?= $o['payment_status'] === 'PAID' ? 'success' : 'warning text-dark' ?>-subtle border">
                                    <?= e($o['payment_status']) ?> (<?= e($o['payment_method']) ?>)
                                </span>
                            </td>
                            <td><span class="badge bg-secondary"><?= e($o['order_status']) ?></span></td>
                            <td class="text-muted text-xs"><?= date('M d, Y H:i', strtotime($o['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php View::endSection(); ?>
