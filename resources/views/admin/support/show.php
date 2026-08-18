<?php

use App\Core\View;
View::layout('layouts.admin');

View::section('title'); ?>Ticket: <?= e($ticket['ticket_number']) ?><?php View::endSection();
View::section('page_title'); ?>Ticket: <?= e($ticket['subject']) ?><?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <a href="<?= url('admin/support') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Helpdesk
    </a>
    <div>
        <span class="badge bg-primary me-1"><?= e($ticket['ticket_number']) ?></span>
        <span class="badge bg-secondary"><?= e($ticket['status']) ?></span>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-700 mb-3"><i class="bi bi-chat-left-dots text-primary me-2"></i>Conversation</h5>

                <div class="d-flex flex-column gap-3 mb-4">
                    <?php foreach ($ticket['messages'] as $msg): ?>
                    <div class="p-3 rounded-3 <?= $msg['sender_type'] === 'admin' ? 'bg-primary-subtle border border-primary-subtle' : 'bg-light border' ?>">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-700 <?= $msg['sender_type'] === 'admin' ? 'text-primary' : 'text-dark' ?>">
                                <?= $msg['sender_type'] === 'admin' ? '<i class="bi bi-shield-check me-1"></i> Support Admin' : e($msg['sender_name']) . ' (Merchant)' ?>
                            </span>
                            <span class="text-muted text-xs"><?= date('M d, Y H:i', strtotime($msg['created_at'])) ?></span>
                        </div>
                        <div class="text-secondary small" style="white-space: pre-wrap;"><?= e($msg['message']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Admin Reply Form -->
                <form method="POST" action="<?= url('admin/support/' . $ticket['id'] . '/reply') ?>" data-loading>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-600">Admin Response</label>
                        <textarea name="message" rows="4" class="form-control" placeholder="Write response to merchant..." required></textarea>
                    </div>

                    <div class="row g-2 align-items-center mb-3">
                        <div class="col-md-6">
                            <label class="form-label small">Change Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="IN_PROGRESS" <?= $ticket['status'] === 'IN_PROGRESS' ? 'selected' : '' ?>>IN PROGRESS</option>
                                <option value="WAITING" <?= $ticket['status'] === 'WAITING' ? 'selected' : '' ?>>WAITING ON MERCHANT</option>
                                <option value="RESOLVED" <?= $ticket['status'] === 'RESOLVED' ? 'selected' : '' ?>>RESOLVED</option>
                                <option value="CLOSED" <?= $ticket['status'] === 'CLOSED' ? 'selected' : '' ?>>CLOSED</option>
                            </select>
                        </div>
                        <div class="col-md-6 text-end pt-4">
                            <button type="submit" class="btn btn-primary fw-600">
                                <i class="bi bi-send me-1"></i> Send Reply & Update Status
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ticket Info -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="fw-700 mb-3">Merchant Information</h6>
                <div class="mb-2 small">
                    <span class="text-muted">Merchant:</span>
                    <div class="fw-600 text-dark"><?= e($ticket['user_name']) ?></div>
                </div>
                <div class="mb-2 small">
                    <span class="text-muted">Business:</span>
                    <div><?= e($ticket['business_name'] ?: 'Not set') ?></div>
                </div>
                <div class="mb-2 small">
                    <span class="text-muted">Email:</span>
                    <div><?= e($ticket['user_email']) ?></div>
                </div>
                <div class="small">
                    <span class="text-muted">Category:</span>
                    <span class="badge bg-light text-dark border"><?= e($ticket['category']) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php View::endSection(); ?>
