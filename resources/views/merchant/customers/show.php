<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Customer: <?= e($customer['name']) ?><?php View::endSection();
View::section('page_title'); ?>Customer Profile: <?= e($customer['name']) ?><?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="<?= url('dashboard/customers') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Customers
    </a>
</div>

<div class="row g-4">
    <!-- Customer Profile Info -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary text-white mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; font-size: 1.5rem; font-weight: 700;">
                    <?= strtoupper(substr($customer['name'], 0, 1)) ?>
                </div>
                <h5 class="fw-700 mb-1"><?= e($customer['name']) ?></h5>
                <p class="text-muted small mb-3"><?= e($customer['email']) ?></p>
                <div class="badge bg-light text-dark border px-3 py-2">
                    <i class="bi bi-telephone me-1"></i> <?= e($customer['mobile'] ?: 'No mobile registered') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Order History -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <h5 class="fw-700 mb-0">Order History (<?= count($customer['orders'] ?? []) ?>)</h5>
                </div>
                <?php if (empty($customer['orders'])): ?>
                    <div class="p-4 text-center text-muted">No orders found for this customer.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th class="text-end">View</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customer['orders'] as $o): ?>
                                <tr>
                                    <td><strong>#<?= e($o['order_number']) ?></strong></td>
                                    <td class="text-muted text-xs"><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                                    <td class="fw-700">₹<?= number_format($o['total'], 2) ?></td>
                                    <td><span class="badge bg-secondary"><?= e($o['order_status']) ?></span></td>
                                    <td class="text-end">
                                        <a href="<?= url('dashboard/orders/' . $o['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Details
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
    </div>
</div>

<?php View::endSection(); ?>
