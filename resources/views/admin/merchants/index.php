<?php

use App\Core\View;
View::layout('layouts.admin');

View::section('title'); ?>Merchants Management<?php View::endSection();
View::section('page_title'); ?>Registered Merchants<?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-700 mb-1">Merchants (<?= $total ?>)</h4>
        <p class="text-muted small mb-0">Manage all merchants, onboarding states, subscriptions and account actions.</p>
    </div>

    <!-- Status filter -->
    <div class="btn-group btn-group-sm">
        <a href="<?= url('admin/merchants') ?>" class="btn btn-<?= empty($status) ? 'primary' : 'outline-secondary' ?>">All</a>
        <a href="<?= url('admin/merchants?status=active') ?>" class="btn btn-<?= $status === 'active' ? 'primary' : 'outline-secondary' ?>">Active</a>
        <a href="<?= url('admin/merchants?status=pending') ?>" class="btn btn-<?= $status === 'pending' ? 'primary' : 'outline-secondary' ?>">Pending</a>
        <a href="<?= url('admin/merchants?status=suspended') ?>" class="btn btn-<?= $status === 'suspended' ? 'primary' : 'outline-secondary' ?>">Suspended</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($merchants)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-shop" style="font-size: 3rem;"></i>
                <p class="mt-2 mb-0">No merchants found matching the selected criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Merchant ID</th>
                            <th>Name & Contact</th>
                            <th>Business Name</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th>Last Login</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($merchants as $m): ?>
                        <tr>
                            <td><code>#M-<?= $m['id'] ?></code></td>
                            <td>
                                <div class="fw-600 text-dark"><?= e($m['name']) ?></div>
                                <div class="text-muted text-xs"><?= e($m['email']) ?> &bull; <?= e($m['mobile'] ?: 'No Phone') ?></div>
                            </td>
                            <td>
                                <span class="fw-500"><?= e($m['business_name'] ?: 'Not configured') ?></span>
                                <div class="text-muted text-xs"><?= e($m['business_category'] ?: 'Category N/A') ?></div>
                            </td>
                            <td>
                                <?php if ($m['status'] === 'active'): ?>
                                    <span class="badge-status badge-success">Active</span>
                                <?php elseif ($m['status'] === 'suspended'): ?>
                                    <span class="badge-status badge-danger">Suspended</span>
                                <?php else: ?>
                                    <span class="badge-status badge-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted text-xs">
                                <?= date('M d, Y', strtotime($m['created_at'])) ?>
                            </td>
                            <td class="text-muted text-xs">
                                <?= $m['last_login_at'] ? date('M d, H:i', strtotime($m['last_login_at'])) : 'Never' ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('admin/merchants/' . $m['id']) ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="bi bi-sliders me-1"></i> Manage
                                </a>
                                <form method="POST" action="<?= url('admin/merchants/' . $m['id'] . '/delete') ?>" style="display:inline;" onsubmit="return confirm('⚠️ PERMANENT DELETE WARNING:\nAre you sure you want to completely delete merchant #<?= $m['id'] ?> (<?= e($m['name']) ?>) and ALL associated store products, orders, and data? This action cannot be undone!');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Merchant">
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

<?php View::endSection(); ?>
