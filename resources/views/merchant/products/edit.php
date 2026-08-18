<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Edit Product<?php View::endSection();
View::section('page_title'); ?>Edit Product: <?= e($product['name']) ?><?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="<?= url('dashboard/products') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Products
    </a>
</div>

<form method="POST" action="<?= url('dashboard/products/' . $product['id'] . '/edit') ?>" enctype="multipart/form-data" data-loading>
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Left 8 Columns -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-700 mb-3">General Information</h5>

                    <div class="mb-3">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= e($product['name']) ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Custom Slug / URL</label>
                            <input type="text" name="slug" class="form-control" value="<?= e($product['slug']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="<?= e($product['sku'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" rows="2" class="form-control"><?= e($product['short_description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Product Description</label>
                        <textarea name="description" rows="5" class="form-control"><?= e($product['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Existing & New Images -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-700 mb-3">Product Images</h5>

                    <?php if (!empty($product['images'])): ?>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Current Photos (check to remove):</label>
                        <div class="d-flex gap-3 flex-wrap">
                            <?php foreach ($product['images'] as $img): ?>
                            <div class="position-relative border rounded p-1 text-center" style="width: 100px;">
                                <img src="<?= url($img) ?>" class="rounded mb-1" style="width: 90px; height: 90px; object-fit: cover;">
                                <div class="form-check text-start small">
                                    <input type="checkbox" name="remove_images[]" value="<?= e($img) ?>" class="form-check-input" id="rm_<?= md5($img) ?>">
                                    <label class="form-check-label text-danger text-xs" for="rm_<?= md5($img) ?>">Remove</label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Upload Additional Images</label>
                        <input type="file" name="images[]" multiple class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

            <!-- Variants Manager -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-700 mb-0">Product Variants</h5>
                            <span class="text-muted text-xs">Manage option variations like sizes, colors and separate inventory.</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addVariantBtn">
                            <i class="bi bi-plus-circle me-1"></i> Add Variant
                        </button>
                    </div>

                    <div id="variantsContainer">
                        <?php if (!empty($product['variants'])): ?>
                            <?php foreach ($product['variants'] as $v): ?>
                            <div class="border rounded p-3 mb-2 bg-light">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-4">
                                        <label class="form-label text-xs mb-1">Variant Name</label>
                                        <input type="text" name="variant_name[]" class="form-control form-control-sm" value="<?= e($v['name']) ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-xs mb-1">Variant SKU</label>
                                        <input type="text" name="variant_sku[]" class="form-control form-control-sm" value="<?= e($v['sku'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label text-xs mb-1">Price (₹)</label>
                                        <input type="number" step="0.01" name="variant_price[]" class="form-control form-control-sm" value="<?= e($v['price']) ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label text-xs mb-1">Stock</label>
                                        <input type="number" name="variant_stock[]" class="form-control form-control-sm" value="<?= e($v['stock']) ?>">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <label class="form-label text-xs mb-1 d-block">&nbsp;</label>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.border').remove()">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 4 Columns -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-700 mb-3">Pricing & Inventory</h5>

                    <div class="mb-3">
                        <label class="form-label">Regular Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control fw-700" value="<?= e($product['price']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Compare-at Price (₹)</label>
                        <input type="number" step="0.01" name="compare_price" class="form-control" value="<?= e($product['compare_price'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total Inventory Stock</label>
                        <input type="number" name="stock" class="form-control" value="<?= e($product['stock']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Low Stock Alert Limit</label>
                        <input type="number" name="low_stock_limit" class="form-control" value="<?= e($product['low_stock_limit']) ?>">
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-700 mb-3">Organization</h5>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- No Category --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control" value="<?= e($product['brand'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="published" <?= $product['status'] === 'published' ? 'selected' : '' ?>>Published (Live)</option>
                            <option value="draft" <?= $product['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="archived" <?= $product['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" <?= $product['is_featured'] ? 'checked' : '' ?>>
                        <label class="form-check-label fw-500" for="is_featured">Feature on Homepage</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-700">
                        <i class="bi bi-check-lg me-1"></i> Update Product
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.getElementById('addVariantBtn').addEventListener('click', function() {
    var container = document.getElementById('variantsContainer');
    var row = document.createElement('div');
    row.className = 'border rounded p-3 mb-2 bg-light';
    row.innerHTML = `
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label text-xs mb-1">Variant Name</label>
                <input type="text" name="variant_name[]" class="form-control form-control-sm" placeholder="Size: L / Color: Blue" required>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs mb-1">Variant SKU</label>
                <input type="text" name="variant_sku[]" class="form-control form-control-sm" placeholder="VAR-SKU">
            </div>
            <div class="col-md-2">
                <label class="form-label text-xs mb-1">Price (₹)</label>
                <input type="number" step="0.01" name="variant_price[]" class="form-control form-control-sm" placeholder="999">
            </div>
            <div class="col-md-2">
                <label class="form-label text-xs mb-1">Stock</label>
                <input type="number" name="variant_stock[]" class="form-control form-control-sm" placeholder="10">
            </div>
            <div class="col-md-1 text-end">
                <label class="form-label text-xs mb-1 d-block">&nbsp;</label>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.border').remove()">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(row);
});
</script>

<?php View::endSection(); ?>
