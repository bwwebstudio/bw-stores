<?php

use App\Core\View;
View::layout('layouts.admin');

View::section('title'); ?>Support Desk<?php View::endSection();
View::section('page_title'); ?>Merchant Support Helpdesk<?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-700 mb-1">Support Tickets (<?= $total ?>)</h4>
        <p class="text-muted small mb-0">Respond to merchant questions, technical requests, and support queries.</p>
    </div>
    <div class="btn-group btn-group-sm">
        <a href="<?= url('admin/support') ?>" class="btn btn-<?= empty($status) ? 'primary' : 'outline-secondary' ?>">All</a>
        <a href="<?= url('admin/support?status=OPEN') ?>" class="btn btn-<?= $status === 'OPEN' ? 'primary' : 'outline-secondary' ?>">Open</a>
        <a href="<?= url('admin/support?status=IN_PROGRESS') ?>" class="btn btn-<?= $status === 'IN_PROGRESS' ? 'primary' : 'outline-secondary' ?>">In Progress</a>
        <a href="<?= url('admin/support?status=RESOLVED') ?>" class="btn btn-<?= $status === 'RESOLVED' ? 'primary' : 'outline-secondary' ?>">Resolved</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($tickets)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-headset" style="font-size: 3rem;"></i>
                <p class="mt-2 mb-0">No support tickets found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Merchant</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td><strong class="text-primary"><?= e($t['ticket_number']) ?></strong></td>
                            <td>
                                <div class="fw-600 text-dark"><?= e($t['user_name']) ?></div>
                                <div class="text-muted text-xs"><?= e($t['business_name'] ?: $t['user_email']) ?></div>
                            </td>
                            <td class="fw-600"><?= e($t['subject']) ?></td>
                            <td><span class="badge bg-light text-dark border"><?= e($t['category']) ?></span></td>
                            <td>
                                <span class="badge bg-<?= $t['priority'] === 'high' ? 'danger' : 'secondary' ?>"><?= strtoupper(e($t['priority'])) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $t['status'] === 'OPEN' ? 'warning text-dark' : ($t['status'] === 'RESOLVED' ? 'success' : 'info') ?>">
                                    <?= e($t['status']) ?>
                                </span>
                            </td>
                            <td class="text-muted text-xs"><?= date('M d, H:i', strtotime($t['created_at'])) ?></td>
                            <td class="text-end">
                                <a href="<?= url('admin/support/' . $t['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-chat-left-dots me-1"></i> Respond
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
