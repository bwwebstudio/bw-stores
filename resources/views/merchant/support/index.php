<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Support Desk<?php View::endSection();
View::section('page_title'); ?>Help & Support<?php View::endSection();

View::section('content'); ?>

<div class="row g-4">
    <!-- Left 4 Columns: Create Ticket -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="fw-700 mb-3"><i class="bi bi-plus-circle text-primary me-2"></i>Open New Ticket</h5>

                <form method="POST" action="<?= url('dashboard/support/create') ?>" data-loading>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Issue connecting payment gateway" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="General">General</option>
                                <option value="Billing">Billing & Subscription</option>
                                <option value="Technical">Technical Issue</option>
                                <option value="Storefront">Storefront / Themes</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detailed Message <span class="text-danger">*</span></label>
                        <textarea name="message" rows="4" class="form-control" placeholder="Describe the issue in detail..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-700">
                        <i class="bi bi-send me-1"></i> Submit Ticket
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right 8 Columns: Tickets List -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <h5 class="fw-700 mb-0">My Support Tickets (<?= count($tickets) ?>)</h5>
                </div>

                <?php if (empty($tickets)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-headset text-muted" style="font-size: 3rem;"></i>
                        <h5 class="fw-700 mt-3">No support tickets</h5>
                        <p class="text-muted small">Need help with your store? Submit a ticket and our support team will help you.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Subject</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tickets as $t): ?>
                                <tr>
                                    <td><strong class="text-primary"><?= e($t['ticket_number']) ?></strong></td>
                                    <td>
                                        <div class="fw-600"><?= e($t['subject']) ?></div>
                                        <div class="text-muted text-xs"><?= $t['message_count'] ?> messages</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= e($t['category']) ?></span></td>
                                    <td>
                                        <?php
                                            $stBadge = match($t['status']) {
                                                'OPEN' => 'badge-info',
                                                'IN_PROGRESS' => 'badge-warning',
                                                'RESOLVED' => 'badge-success',
                                                default => 'badge-gray',
                                            };
                                        ?>
                                        <span class="badge-status <?= $stBadge ?>"><?= $t['status'] ?></span>
                                    </td>
                                    <td class="text-muted text-xs"><?= date('M d, H:i', strtotime($t['updated_at'])) ?></td>
                                    <td class="text-end">
                                        <a href="<?= url('dashboard/support/' . $t['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-chat-left-text"></i> Open
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
