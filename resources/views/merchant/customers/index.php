<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Customers<?php View::endSection();
View::section('page_title'); ?>Merchant Customer Base<?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-700 mb-1">Customers (<?= $total ?>)</h4>
        <p class="text-muted small mb-0">View customer profiles and purchase metrics scoped strictly to your store.</p>
    </div>
</div>

<!-- Search -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= url('dashboard/customers') ?>" class="row g-2 align-items-center">
            <div class="col-md-8">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search customer by name, email, or mobile..." value="<?= e($filters['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-dark w-100">Search</button>
                <a href="<?= url('dashboard/customers') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Customer Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($customers)): ?>
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                <h5 class="fw-700 mt-3">No customers registered yet</h5>
                <p class="text-muted small">Customers who checkout on your store will automatically appear here.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Total Orders</th>
                            <th>Total Spent</th>
                            <th>Last Order</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $c): ?>
                        <tr>
                            <td>
                                <div class="fw-600 text-dark"><?= e($c['name']) ?></div>
                            </td>
                            <td><?= e($c['email']) ?></td>
                            <td><?= e($c['mobile'] ?: 'N/A') ?></td>
                            <td><span class="badge bg-primary-subtle text-primary border"><?= $c['total_orders'] ?> orders</span></td>
                            <td class="fw-700 text-dark">₹<?= number_format($c['total_spent'], 2) ?></td>
                            <td class="text-muted text-xs">
                                <?= $c['last_order_at'] ? date('M d, Y', strtotime($c['last_order_at'])) : 'None' ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('dashboard/customers/' . $c['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View History
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
