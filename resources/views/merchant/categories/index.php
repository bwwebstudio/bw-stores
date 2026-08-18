<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Categories<?php View::endSection();
View::section('page_title'); ?>Product Categories<?php View::endSection();

View::section('content'); ?>

<div class="row g-4">
    <!-- Left 4 Columns: Create Category Form -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="fw-700 mb-3"><i class="bi bi-plus-circle text-primary me-2"></i>Add Category</h5>

                <form method="POST" action="<?= url('dashboard/categories/create') ?>" enctype="multipart/form-data" data-loading>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Men's Footwear" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Custom Slug (optional)</label>
                        <input type="text" name="slug" class="form-control" placeholder="mens-footwear">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Optional description shown on category page..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category Banner Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-700">
                        <i class="bi bi-check-lg me-1"></i> Save Category
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right 8 Columns: Category List Table -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-700 mb-0">All Categories (<?= count($categories) ?>)</h5>
                </div>

                <?php if (empty($categories)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-tags text-muted" style="font-size: 3rem;"></i>
                        <h5 class="fw-700 mt-3">No categories yet</h5>
                        <p class="text-muted small">Create categories to organize your store inventory.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">Image</th>
                                    <th>Category</th>
                                    <th>Slug</th>
                                    <th>Products</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $c): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($c['image'])): ?>
                                            <img src="<?= url($c['image']) ?>" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted" style="width: 40px; height: 40px;">
                                                <i class="bi bi-tag"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-600 text-dark"><?= e($c['name']) ?></div>
                                        <?php if (!empty($c['description'])): ?>
                                            <div class="text-muted text-xs"><?= e(str_truncate($c['description'], 45)) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><code class="text-xs">/category/<?= e($c['slug']) ?></code></td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= $c['product_count'] ?> products</span>
                                    </td>
                                    <td>
                                        <?php if ($c['status'] === 'active'): ?>
                                            <span class="badge-status badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge-status badge-gray">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <form method="POST" action="<?= url('dashboard/categories/' . $c['id'] . '/delete') ?>" style="display:inline;" onsubmit="return confirm('Delete this category? Products inside will be set to uncategorized.');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
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
    </div>
</div>

<?php View::endSection(); ?>
