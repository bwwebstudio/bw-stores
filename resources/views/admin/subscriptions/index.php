<?php

use App\Core\View;
View::layout('layouts.admin');

View::section('title'); ?>All Subscriptions<?php View::endSection();
View::section('page_title'); ?>Merchant SaaS Subscriptions<?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-700 mb-1">Subscriptions (<?= $total ?>)</h4>
        <p class="text-muted small mb-0">Platform subscriptions running under the flat ₹999/month SaaS model.</p>
    </div>
    <div class="btn-group btn-group-sm">
        <a href="<?= url('admin/subscriptions') ?>" class="btn btn-<?= empty($status) ? 'primary' : 'outline-secondary' ?>">All</a>
        <a href="<?= url('admin/subscriptions?status=active') ?>" class="btn btn-<?= $status === 'active' ? 'primary' : 'outline-secondary' ?>">Active</a>
        <a href="<?= url('admin/subscriptions?status=past_due') ?>" class="btn btn-<?= $status === 'past_due' ? 'primary' : 'outline-secondary' ?>">Past Due</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($subscriptions)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-credit-card-2-back" style="font-size: 3rem;"></i>
                <p class="mt-2 mb-0">No subscriptions found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Merchant</th>
                            <th>Plan Name</th>
                            <th>Rate</th>
                            <th>Status</th>
                            <th>Current Period</th>
                            <th>Expires On</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscriptions as $sub): ?>
                        <tr>
                            <td>
                                <div class="fw-600 text-dark"><?= e($sub['merchant_name']) ?></div>
                                <div class="text-muted text-xs"><?= e($sub['merchant_email']) ?></div>
                            </td>
                            <td><strong><?= e($sub['plan_name']) ?></strong></td>
                            <td class="fw-700 text-success">₹<?= number_format($sub['plan_price'], 2) ?>/mo</td>
                            <td>
                                <?php if ($sub['status'] === 'active'): ?>
                                    <span class="badge-status badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge-status badge-warning"><?= e($sub['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted text-xs">
                                <?= date('M d, Y', strtotime($sub['current_period_start'])) ?> - <?= date('M d, Y', strtotime($sub['current_period_end'])) ?>
                            </td>
                            <td class="fw-600 text-dark text-xs">
                                <?= $sub['expires_at'] ? date('M d, Y', strtotime($sub['expires_at'])) : 'Ongoing' ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('admin/merchants/' . $sub['merchant_id']) ?>" class="btn btn-sm btn-outline-primary">
                                    Manage
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
