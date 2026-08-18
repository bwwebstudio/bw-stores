<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Inventory & Stock<?php View::endSection();
View::section('page_title'); ?>Inventory Tracking<?php View::endSection();

View::section('content'); ?>

<div class="row g-4">
    <!-- Stock Levels Table -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="fw-700 mb-0"><i class="bi bi-boxes text-primary me-2"></i>Current Stock Levels</h5>
                    <form method="GET" action="<?= url('dashboard/inventory') ?>" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Filter product..." value="<?= e($search ?? '') ?>">
                        <button type="submit" class="btn btn-sm btn-dark">Search</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th class="text-end">Quick Adjust</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): ?>
                            <tr>
                                <td>
                                    <div class="fw-600"><?= e($p['name']) ?></div>
                                    <?php if (!empty($p['variants'])): ?>
                                        <div class="text-muted text-xs"><?= count($p['variants']) ?> variant(s) available</div>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= e($p['sku'] ?: 'N/A') ?></code></td>
                                <td class="fw-700 fs-6"><?= $p['stock'] ?></td>
                                <td>
                                    <?php if ($p['stock'] <= 0): ?>
                                        <span class="badge bg-danger">Out of stock</span>
                                    <?php elseif ($p['stock'] <= $p['low_stock_limit']): ?>
                                        <span class="badge bg-warning text-dark">Low stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">In stock</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adjustModal_<?= $p['id'] ?>">
                                        <i class="bi bi-sliders me-1"></i> Adjust
                                    </button>

                                    <!-- Adjust Modal -->
                                    <div class="modal fade text-start" id="adjustModal_<?= $p['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="<?= url('dashboard/inventory/adjust') ?>">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-700">Adjust Stock: <?= e($p['name']) ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-muted small">Current Stock: <strong><?= $p['stock'] ?></strong></p>

                                                        <?php if (!empty($p['variants'])): ?>
                                                        <div class="mb-3">
                                                            <label class="form-label">Select Variant (Optional)</label>
                                                            <select name="variant_id" class="form-select">
                                                                <option value="">Base Product Stock (<?= $p['stock'] ?>)</option>
                                                                <?php foreach ($p['variants'] as $v): ?>
                                                                    <option value="<?= $v['id'] ?>"><?= e($v['name']) ?> (Current: <?= $v['stock'] ?>)</option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <?php endif; ?>

                                                        <div class="mb-3">
                                                            <label class="form-label">New Exact Stock Quantity</label>
                                                            <input type="number" name="new_stock" class="form-control fw-700" value="<?= $p['stock'] ?>" min="0" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Reason / Adjustment Note</label>
                                                            <input type="text" name="reason" class="form-control" placeholder="e.g. Restocked shipment #884, damaged item write-off" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-sm btn-primary">Save Adjustment</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Activity History -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body p-3">
                <h5 class="fw-700 mb-3"><i class="bi bi-clock-history text-primary me-2"></i>Stock Activity Log</h5>

                <?php if (empty($logs)): ?>
                    <p class="text-muted small mb-0">No recent inventory adjustments recorded.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush small">
                        <?php foreach ($logs as $l): ?>
                        <div class="list-group-item px-0 py-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-600 text-dark"><?= e($l['product_name']) ?></span>
                                <?php if ($l['type'] === 'addition'): ?>
                                    <span class="badge bg-success-subtle text-success">+<?= $l['quantity'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger">-<?= $l['quantity'] ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="text-muted text-xs mt-1">
                                <span><?= e($l['notes'] ?: ucfirst($l['type'])) ?></span> &bull; 
                                <span><?= date('M d, H:i', strtotime($l['created_at'])) ?></span>
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
