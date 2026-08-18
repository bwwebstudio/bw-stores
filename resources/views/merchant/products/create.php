<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Add New Product<?php View::endSection();
View::section('page_title'); ?>Add Product<?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="<?= url('dashboard/products') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Products
    </a>
</div>

<form method="POST" action="<?= url('dashboard/products/create') ?>" enctype="multipart/form-data" data-loading>
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Left 8 Columns: Main Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-700 mb-3">General Information</h5>

                    <div class="mb-3">
                        <label class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Classic Organic Cotton T-Shirt" value="<?= e(old('name')) ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Custom Slug / URL</label>
                            <input type="text" name="slug" class="form-control" placeholder="classic-cotton-tshirt" value="<?= e(old('slug')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU (Stock Keeping Unit)</label>
                            <input type="text" name="sku" class="form-control" placeholder="TSH-001" value="<?= e(old('sku')) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description</label>
                        <textarea name="short_description" rows="2" class="form-control" placeholder="Brief summary displayed on listings..."><?= e(old('short_description')) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Full Product Description</label>
                        <textarea name="description" rows="5" class="form-control" placeholder="Detailed product specifications, materials, care instructions..."><?= e(old('description')) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Media Upload -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-700 mb-3">Product Images</h5>
                    <div class="mb-3">
                        <label class="form-label">Select high quality product photos</label>
                        <input type="file" name="images[]" multiple class="form-control" accept="image/*">
                        <div class="text-muted text-xs mt-1">Supports JPG, PNG, WebP (Max 5MB per image). You can select multiple files.</div>
                    </div>
                </div>
            </div>

            <!-- Variants Manager -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-700 mb-0">Product Variants</h5>
                            <span class="text-muted text-xs">Add options like Size (S, M, L) or Color (Black, Blue) with individual prices & stocks.</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addVariantBtn">
                            <i class="bi bi-plus-circle me-1"></i> Add Variant
                        </button>
                    </div>

                    <div id="variantsContainer">
                        <!-- Dynamic Variant Rows -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 4 Columns: Pricing, Category, Status -->
        <div class="col-lg-4">
            <!-- Pricing & Inventory -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-700 mb-3">Pricing & Inventory</h5>

                    <div class="mb-3">
                        <label class="form-label">Regular Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control fw-700" placeholder="999.00" value="<?= e(old('price')) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Compare-at / Original Price (₹)</label>
                        <input type="number" step="0.01" name="compare_price" class="form-control" placeholder="1499.00" value="<?= e(old('compare_price')) ?>">
                        <div class="text-muted text-xs mt-1">Shows a strikethrough discount on the storefront.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total Inventory Stock</label>
                        <input type="number" name="stock" class="form-control" placeholder="50" value="<?= e(old('stock', '10')) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Low Stock Alert Limit</label>
                        <input type="number" name="low_stock_limit" class="form-control" placeholder="5" value="<?= e(old('low_stock_limit', '5')) ?>">
                    </div>
                </div>
            </div>

            <!-- Organization & Status -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-700 mb-3">Organization</h5>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">-- No Category --</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Brand / Manufacturer</label>
                        <input type="text" name="brand" class="form-control" placeholder="e.g. Nike, Zara, Handcrafted">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="published">Published (Live)</option>
                            <option value="draft">Draft</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured">
                        <label class="form-check-label fw-500" for="is_featured">Feature on Storefront Homepage</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-700">
                        <i class="bi bi-check-lg me-1"></i> Save & Publish Product
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
                <label class="form-label text-xs mb-1">Variant Name (e.g. Size: L, Color: Black)</label>
                <input type="text" name="variant_name[]" class="form-control form-control-sm" placeholder="Size: L / Color: Blue" required>
            </div>
            <div class="col-md-3">
                <label class="form-label text-xs mb-1">Variant SKU</label>
                <input type="text" name="variant_sku[]" class="form-control form-control-sm" placeholder="VAR-SKU-01">
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
