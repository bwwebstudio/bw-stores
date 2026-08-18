<?php

use App\Core\View;
View::layout('layouts.admin');

View::section('title'); ?>SaaS Subscription Payments<?php View::endSection();
View::section('page_title'); ?>Merchant Subscription Payments & UPI Verifications<?php View::endSection();

View::section('content'); ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-800 text-dark mb-1">Platform Revenue Transactions</h5>
                <span class="text-muted small">Merchant SaaS tier subscriptions & manual UPI payment approvals.</span>
            </div>
            <span class="badge bg-primary fs-6 px-3 py-2">Total Invoices: <?= $total ?></span>
        </div>

        <?php if (empty($payments)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-wallet2 fs-1 text-muted"></i>
                <h5 class="mt-3">No payment records found.</h5>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Ref / Payment ID</th>
                            <th>Merchant</th>
                            <th>Plan</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>UTR / Transaction Ref</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Verification Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $pay): ?>
                        <tr>
                            <td><code><?= e($pay['gateway_payment_id'] ?: 'PAY-' . $pay['id']) ?></code></td>
                            <td>
                                <div class="fw-700 text-dark"><?= e($pay['user_name']) ?></div>
                                <div class="text-muted text-xs"><?= e($pay['user_email']) ?> &bull; <?= e($pay['business_name'] ?: 'No Brand') ?></div>
                            </td>
                            <td>
                                <?php if (!empty($pay['plan_name'])): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-xs"><?= e($pay['plan_name']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark text-xs">Standard Plan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $pay['payment_method'] === 'UPI' ? 'info' : 'primary' ?> text-uppercase">
                                    <?= e($pay['payment_method'] ?: 'RAZORPAY') ?>
                                </span>
                            </td>
                            <td class="fw-800 text-success fs-6">₹<?= number_format($pay['amount'], 2) ?></td>
                            <td>
                                <?php if (!empty($pay['transaction_ref'])): ?>
                                    <code class="fw-700 text-dark font-monospace"><?= e($pay['transaction_ref']) ?></code>
                                <?php else: ?>
                                    <span class="text-muted text-xs">&mdash;</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($pay['status'] === 'paid'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">PAID & ACTIVE</span>
                                <?php elseif ($pay['status'] === 'pending_verification'): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1">PENDING VERIFICATION</span>
                                <?php elseif ($pay['status'] === 'rejected'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">REJECTED</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= strtoupper(e($pay['status'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted text-xs"><?= date('M d, Y H:i', strtotime($pay['created_at'])) ?></td>
                            <td class="text-end">
                                <?php if ($pay['status'] === 'pending_verification'): ?>
                                    <div class="d-inline-flex gap-1">
                                        <form method="POST" action="<?= url('admin/payments/' . $pay['id'] . '/approve') ?>" onsubmit="return confirm('Verify and approve this UPI payment? Store will be activated for 30 days immediately.');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-success fw-700">
                                                <i class="bi bi-check-lg me-1"></i> Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="<?= url('admin/payments/' . $pay['id'] . '/reject') ?>" onsubmit="return confirm('Reject this UPI transaction ref?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                <?php elseif ($pay['status'] === 'paid'): ?>
                                    <span class="text-success small fw-600"><i class="bi bi-check2-circle me-1"></i> Verified</span>
                                <?php else: ?>
                                    <span class="text-muted text-xs">&mdash;</span>
                                <?php endif; ?>
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
