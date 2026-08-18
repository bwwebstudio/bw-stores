<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Coupons & Discounts<?php View::endSection();
View::section('page_title'); ?>Promotion Engine<?php View::endSection();

View::section('content'); ?>

<div class="row g-4">
    <!-- Left 4 Columns: Create Coupon Form -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="fw-700 mb-3"><i class="bi bi-ticket-perforated text-primary me-2"></i>Create Coupon</h5>

                <form method="POST" action="<?= url('dashboard/coupons/create') ?>" data-loading>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control fw-700 text-uppercase" placeholder="e.g. FESTIVE20, FLAT100" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Discount Type</label>
                            <select name="type" class="form-select">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (₹)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Value <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="value" class="form-control fw-700" placeholder="20" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Minimum Order Amount (₹)</label>
                        <input type="number" step="0.01" name="min_order" class="form-control" placeholder="0.00" value="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Max Discount Cap (₹) <span class="text-muted text-xs">(For % only)</span></label>
                        <input type="number" step="0.01" name="max_discount" class="form-control" placeholder="500">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Total Usage Limit</label>
                            <input type="number" name="usage_limit" class="form-control" placeholder="100">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" name="expires_at" class="form-control">
                        </div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                        <label class="form-check-label fw-500" for="is_active">Enable Coupon Now</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-700">
                        <i class="bi bi-plus-lg me-1"></i> Create Coupon
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right 8 Columns: Coupon List Table -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <h5 class="fw-700 mb-0">Active Coupons (<?= count($coupons) ?>)</h5>
                </div>

                <?php if (empty($coupons)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-ticket-perforated text-muted" style="font-size: 3rem;"></i>
                        <h5 class="fw-700 mt-3">No promotional coupons yet</h5>
                        <p class="text-muted small">Create discounts to increase conversions and order values.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Discount</th>
                                    <th>Min Order</th>
                                    <th>Usage</th>
                                    <th>Expiry</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($coupons as $cp): ?>
                                <tr>
                                    <td><strong class="text-primary fs-6"><?= e($cp['code']) ?></strong></td>
                                    <td>
                                        <?php if ($cp['type'] === 'percentage'): ?>
                                            <span class="badge bg-info-subtle text-info border"><?= $cp['value'] ?>% OFF</span>
                                        <?php else: ?>
                                            <span class="badge bg-success-subtle text-success border">₹<?= number_format($cp['value'], 2) ?> FLAT</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>₹<?= number_format($cp['min_order'], 2) ?></td>
                                    <td><?= $cp['usage_count'] ?><?= $cp['usage_limit'] ? ' / ' . $cp['usage_limit'] : '' ?></td>
                                    <td class="text-muted text-xs">
                                        <?= $cp['expires_at'] ? date('M d, Y', strtotime($cp['expires_at'])) : 'Never' ?>
                                    </td>
                                    <td>
                                        <?php if ($cp['is_active']): ?>
                                            <span class="badge-status badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge-status badge-gray">Disabled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <form method="POST" action="<?= url('dashboard/coupons/' . $cp['id'] . '/delete') ?>" style="display:inline;" onsubmit="return confirm('Delete this coupon?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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
