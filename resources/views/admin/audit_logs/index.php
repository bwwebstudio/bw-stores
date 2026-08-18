<?php

use App\Core\View;
View::layout('layouts.admin');

View::section('title'); ?>Audit Logs<?php View::endSection();
View::section('page_title'); ?>Security Audit Trail<?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-700 mb-1">System Audit Logs (<?= $total ?>)</h4>
        <p class="text-muted small mb-0">Immutable compliance log recording user events, logins, and administrative changes.</p>
    </div>
    <form method="GET" action="<?= url('admin/audit-logs') ?>" class="d-flex gap-2">
        <input type="text" name="action" class="form-control form-control-sm" placeholder="Filter action..." value="<?= e($action ?? '') ?>">
        <button type="submit" class="btn btn-sm btn-dark">Filter</button>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($logs)): ?>
            <div class="p-5 text-center text-muted">No audit events recorded matching filter.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-xs">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>User / Entity</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $l): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border font-monospace"><?= e($l['action']) ?></span></td>
                            <td>
                                <div><?= e($l['user_name'] ?: 'Guest / System') ?></div>
                                <div class="text-muted text-xs"><?= e($l['user_email']) ?></div>
                            </td>
                            <td class="text-secondary"><?= e($l['description']) ?></td>
                            <td><code><?= e($l['ip_address'] ?: '127.0.0.1') ?></code></td>
                            <td class="text-muted"><?= date('M d, Y H:i:s', strtotime($l['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php View::endSection(); ?>
