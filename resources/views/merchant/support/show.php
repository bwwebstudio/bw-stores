<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Ticket <?= e($ticket['ticket_number']) ?><?php View::endSection();
View::section('page_title'); ?>Ticket: <?= e($ticket['subject']) ?><?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <a href="<?= url('dashboard/support') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Tickets
    </a>
    <div>
        <span class="badge bg-primary me-1"><?= e($ticket['ticket_number']) ?></span>
        <span class="badge bg-secondary"><?= e($ticket['status']) ?></span>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Message Thread -->
        <div class="card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-700 mb-3"><i class="bi bi-chat-left-dots text-primary me-2"></i>Conversation Thread</h5>

                <div class="d-flex flex-column gap-3 mb-4">
                    <?php foreach ($ticket['messages'] as $msg): ?>
                    <div class="p-3 rounded-3 <?= $msg['sender_type'] === 'admin' ? 'bg-primary-subtle border border-primary-subtle' : 'bg-light border' ?>">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-700 <?= $msg['sender_type'] === 'admin' ? 'text-primary' : 'text-dark' ?>">
                                <?= $msg['sender_type'] === 'admin' ? '<i class="bi bi-shield-check me-1"></i> BW Store Support Team' : e($msg['sender_name']) ?>
                            </span>
                            <span class="text-muted text-xs"><?= date('M d, Y H:i', strtotime($msg['created_at'])) ?></span>
                        </div>
                        <div class="text-secondary small" style="white-space: pre-wrap;"><?= e($msg['message']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Reply Form -->
                <?php if ($ticket['status'] !== 'CLOSED'): ?>
                <form method="POST" action="<?= url('dashboard/support/' . $ticket['id'] . '/reply') ?>" data-loading>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-600">Send a Reply</label>
                        <textarea name="message" rows="3" class="form-control" placeholder="Type your response here..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary fw-600">
                        <i class="bi bi-send me-1"></i> Post Reply
                    </button>
                </form>
                <?php else: ?>
                    <div class="alert alert-secondary mb-0">This support ticket has been closed.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Ticket Meta -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-700 mb-3">Ticket Information</h6>
                <div class="mb-2 small">
                    <span class="text-muted">Subject:</span>
                    <div class="fw-600 text-dark"><?= e($ticket['subject']) ?></div>
                </div>
                <div class="mb-2 small">
                    <span class="text-muted">Category:</span>
                    <div class="fw-600"><?= e($ticket['category']) ?></div>
                </div>
                <div class="mb-2 small">
                    <span class="text-muted">Priority:</span>
                    <span class="badge bg-secondary"><?= strtoupper(e($ticket['priority'])) ?></span>
                </div>
                <div class="small">
                    <span class="text-muted">Created:</span>
                    <div><?= date('M d, Y H:i', strtotime($ticket['created_at'])) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php View::endSection(); ?>
