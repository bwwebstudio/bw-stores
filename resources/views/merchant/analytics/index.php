<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Analytics & Reports<?php View::endSection();
View::section('page_title'); ?>Sales Analytics & Insights<?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-700 mb-1">Store Performance</h4>
        <p class="text-muted small mb-0">Real-time revenue metrics, order velocity, and customer purchasing trends.</p>
    </div>
    <!-- Time Range Selector -->
    <div class="btn-group btn-group-sm">
        <a href="<?= url('dashboard/analytics?range=today') ?>" class="btn btn-<?= $range === 'today' ? 'primary' : 'outline-secondary' ?>">Today</a>
        <a href="<?= url('dashboard/analytics?range=7') ?>" class="btn btn-<?= $range === '7' ? 'primary' : 'outline-secondary' ?>">Last 7 Days</a>
        <a href="<?= url('dashboard/analytics?range=30') ?>" class="btn btn-<?= $range === '30' ? 'primary' : 'outline-secondary' ?>">Last 30 Days</a>
        <a href="<?= url('dashboard/analytics?range=all') ?>" class="btn btn-<?= $range === 'all' ? 'primary' : 'outline-secondary' ?>">All Time</a>
    </div>
</div>

<!-- 4 Key Stat Cards -->
<div class="dashboard-stats mb-4">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(37, 99, 235, 0.1);color:var(--color-primary);">
            <i class="bi bi-currency-rupee"></i>
        </div>
        <div class="stat-value">₹<?= number_format($totalSales, 2) ?></div>
        <div class="stat-label">Total Revenue</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--color-success-light);color:var(--color-success);">
            <i class="bi bi-bag-check"></i>
        </div>
        <div class="stat-value"><?= $totalOrders ?></div>
        <div class="stat-label">Total Orders Placed</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--color-info-light);color:var(--color-info);">
            <i class="bi bi-cart-check"></i>
        </div>
        <div class="stat-value">₹<?= number_format($avgOrderValue, 2) ?></div>
        <div class="stat-label">Average Order Value (AOV)</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--color-warning-light);color:var(--color-warning);">
            <i class="bi bi-people"></i>
        </div>
        <div class="stat-value"><?= $totalCustomers ?></div>
        <div class="stat-label">Unique Customers</div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Daily Revenue Chart Table -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-700 mb-3"><i class="bi bi-graph-up text-primary me-2"></i>Daily Sales Breakdown (Recent Activity)</h5>

                <?php if (empty($dailySales)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bar-chart" style="font-size: 2.5rem;"></i>
                        <p class="mt-2 mb-0">No order revenue recorded in this time period.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Orders Placed</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dailySales as $d): ?>
                                <tr>
                                    <td><i class="bi bi-calendar-event text-muted me-1"></i> <?= date('D, M d, Y', strtotime($d['sale_date'])) ?></td>
                                    <td><span class="badge bg-light text-dark border"><?= $d['orders_count'] ?> orders</span></td>
                                    <td class="text-end fw-700 text-success">₹<?= number_format($d['revenue'], 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="fw-700 mb-3"><i class="bi bi-trophy text-primary me-2"></i>Top 5 Best Sellers</h5>

                <?php if (empty($topProducts)): ?>
                    <p class="text-muted small">No product sales yet.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($topProducts as $tp): ?>
                        <div class="list-group-item px-0 py-2 border-bottom">
                            <div class="fw-600 text-dark"><?= e($tp['product_name']) ?></div>
                            <div class="d-flex justify-content-between text-xs text-muted mt-1">
                                <span><?= $tp['total_qty'] ?> units sold</span>
                                <span class="fw-700 text-primary">₹<?= number_format($tp['total_revenue'], 2) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php View::endSection(); ?>
