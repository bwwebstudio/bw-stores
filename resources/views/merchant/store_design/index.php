<?php

use App\Core\View;
View::layout('layouts.dashboard');

View::section('title'); ?>Store Design & Themes<?php View::endSection();
View::section('page_title'); ?>Storefront Customizer & Themes<?php View::endSection();

View::section('content'); ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-800 text-dark mb-1">Store Appearance & Design Engine</h4>
        <p class="text-secondary small mb-0">Customize your public online storefront layout, high-converting themes, branding, and color scheme.</p>
    </div>
    <?php if ($store): ?>
    <a href="<?= url('store/' . $store['slug']) ?>" target="_blank" class="btn btn-outline-primary fw-700 shadow-sm">
        <i class="bi bi-box-arrow-up-right me-1"></i> Preview Live Storefront
    </a>
    <?php endif; ?>
</div>

<form method="POST" action="<?= url('dashboard/store-design') ?>" enctype="multipart/form-data" data-loading>
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Left 7 Columns -->
        <div class="col-lg-7">
            <!-- Theme Selection -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-800 text-dark mb-0"><i class="bi bi-palette2 text-primary me-2"></i>1. Select Storefront Theme</h5>
                        <span class="badge bg-primary-subtle text-primary fw-800 text-xs px-2 py-1">3 THEMES READY</span>
                    </div>
                    <p class="text-secondary small mb-4">Choose a high-converting layout designed for your product catalogue and target audience:</p>

                    <div class="row g-3">
                        <!-- Modern Theme -->
                        <div class="col-md-4">
                            <label class="card p-3 text-center h-100 border-2 rounded-4 cursor-pointer theme-select-card position-relative transition-all <?= ($settings['theme_name'] ?? 'modern') === 'modern' ? 'border-primary bg-primary-subtle shadow' : 'border bg-white shadow-sm' ?>" style="cursor: pointer;" onclick="selectThemeCard(this)">
                                <span class="badge bg-primary text-white position-absolute top-0 start-50 translate-middle text-xs fw-800">POPULAR</span>
                                <input type="radio" name="theme_name" value="modern" <?= ($settings['theme_name'] ?? 'modern') === 'modern' ? 'checked' : '' ?> class="form-check-input mx-auto mb-2 mt-2">
                                <div class="fw-800 text-dark fs-6 mb-1">⚡ Modern</div>
                                <div class="text-secondary text-xs" style="line-height: 1.4;">High-speed cards, universal grid, instant add-to-cart.</div>
                                <div class="mt-2 text-xs fw-700 text-primary">Best for: All Retail</div>
                            </label>
                        </div>

                        <!-- Fashion Theme -->
                        <div class="col-md-4">
                            <label class="card p-3 text-center h-100 border-2 rounded-4 cursor-pointer theme-select-card position-relative transition-all <?= ($settings['theme_name'] ?? '') === 'fashion' ? 'border-primary bg-primary-subtle shadow' : 'border bg-white shadow-sm' ?>" style="cursor: pointer;" onclick="selectThemeCard(this)">
                                <span class="badge bg-warning text-dark position-absolute top-0 start-50 translate-middle text-xs fw-800">EDITORIAL</span>
                                <input type="radio" name="theme_name" value="fashion" <?= ($settings['theme_name'] ?? '') === 'fashion' ? 'checked' : '' ?> class="form-check-input mx-auto mb-2 mt-2">
                                <div class="fw-800 text-dark fs-6 mb-1">✨ Fashion & Luxury</div>
                                <div class="text-secondary text-xs" style="line-height: 1.4;">Editorial typography, large hero imagery & lifestyle lookbook.</div>
                                <div class="mt-2 text-xs fw-700 text-primary">Best for: Apparel & Beauty</div>
                            </label>
                        </div>

                        <!-- Business Theme -->
                        <div class="col-md-4">
                            <label class="card p-3 text-center h-100 border-2 rounded-4 cursor-pointer theme-select-card position-relative transition-all <?= ($settings['theme_name'] ?? '') === 'business' ? 'border-primary bg-primary-subtle shadow' : 'border bg-white shadow-sm' ?>" style="cursor: pointer;" onclick="selectThemeCard(this)">
                                <span class="badge bg-dark text-white position-absolute top-0 start-50 translate-middle text-xs fw-800">CORPORATE</span>
                                <input type="radio" name="theme_name" value="business" <?= ($settings['theme_name'] ?? '') === 'business' ? 'checked' : '' ?> class="form-check-input mx-auto mb-2 mt-2">
                                <div class="fw-800 text-dark fs-6 mb-1">💼 Bold Business</div>
                                <div class="text-secondary text-xs" style="line-height: 1.4;">Structured catalogue, bold feature blocks & B2B/tech layout.</div>
                                <div class="mt-2 text-xs fw-700 text-primary">Best for: Gadgets & FMCG</div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hero Banner Customization -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-800 text-dark mb-3"><i class="bi bi-image-fill text-primary me-2"></i>2. Homepage Hero Banner & Tagline</h5>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">Hero Banner Headline <span class="text-danger">*</span></label>
                        <input type="text" name="hero_title" class="form-control fw-600" placeholder="e.g. Elevate Your Everyday Lifestyle" value="<?= e($settings['hero_title'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">Hero Subtitle / Description</label>
                        <textarea name="hero_subtitle" rows="2" class="form-control" placeholder="Short inspiring call to action for your storefront visitors..."><?= e($settings['hero_subtitle'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">Hero Banner Image (Optional)</label>
                        <?php if (!empty($settings['hero_image'])): ?>
                            <div class="mb-2 position-relative">
                                <img src="<?= url($settings['hero_image']) ?>" class="rounded-3 border shadow-sm" style="max-height: 140px; width: 100%; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="hero_image" class="form-control" accept="image/*">
                        <input type="hidden" name="existing_hero_image" value="<?= e($settings['hero_image'] ?? '') ?>">
                        <div class="text-muted text-xs mt-1">Recommended dimension: 1920x600px. JPG, PNG or WebP format.</div>
                    </div>
                </div>
            </div>

            <!-- Contact & Social Links -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-800 text-dark mb-3"><i class="bi bi-chat-heart-fill text-primary me-2"></i>3. Customer Support & Social Channels</h5>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-700 text-xs text-dark">WhatsApp Support Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-success-subtle text-success"><i class="bi bi-whatsapp"></i></span>
                                <input type="tel" name="whatsapp_number" class="form-control" placeholder="919876543210" value="<?= e($settings['whatsapp_number'] ?? '') ?>">
                            </div>
                            <div class="text-muted text-xs mt-1">Floating WhatsApp chat widget appears on storefront.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-700 text-xs text-dark">Support Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="contact_email" class="form-control" placeholder="support@yourbrand.com" value="<?= e($settings['contact_email'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-700 text-xs text-dark">Instagram Profile URL</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-instagram text-danger"></i></span>
                                <input type="url" name="instagram_url" class="form-control" placeholder="https://instagram.com/yourhandle" value="<?= e($settings['instagram_url'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-700 text-xs text-dark">Facebook Page URL</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-facebook text-primary"></i></span>
                                <input type="url" name="facebook_url" class="form-control" placeholder="https://facebook.com/yourpage" value="<?= e($settings['facebook_url'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">Physical Store / Return Address</label>
                        <textarea name="business_address" rows="2" class="form-control" placeholder="123 Fashion Street, Mumbai, India..."><?= e($settings['business_address'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">Footer Tagline Text</label>
                        <input type="text" name="footer_text" class="form-control" placeholder="Handcrafted with passion in India. All rights reserved." value="<?= e($settings['footer_text'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 5 Columns: Branding & Colors -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-800 text-dark mb-3"><i class="bi bi-brush-fill text-primary me-2"></i>Branding & Visual Tokens</h5>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">Storefront Public Name <span class="text-danger">*</span></label>
                        <input type="text" name="store_name" class="form-control form-control-lg fw-800 text-dark" value="<?= e($store['name'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">Store Logo</label>
                        <?php if (!empty($store['logo'])): ?>
                            <div class="mb-2 p-2 bg-light rounded-3 border text-center">
                                <img src="<?= url($store['logo']) ?>" class="img-fluid" style="max-height: 70px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <div class="text-muted text-xs mt-1">High resolution PNG / SVG with transparent background recommended.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-700 text-xs text-dark">Favicon (Tab Icon)</label>
                        <?php if (!empty($store['favicon'])): ?>
                            <div class="mb-2 p-1 bg-light rounded-2 border d-inline-block">
                                <img src="<?= url($store['favicon']) ?>" style="width: 32px; height: 32px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="favicon" class="form-control" accept="image/*">
                    </div>

                    <div class="p-3 bg-light rounded-3 border mb-4">
                        <div class="fw-700 text-dark text-xs mb-2"><i class="bi bi-droplet-half text-primary me-1"></i> Palette Color Accent</div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-600 text-xs text-secondary">Primary Button Color</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" name="primary_color" id="primaryColorInput" class="form-control form-control-color" value="<?= e($settings['primary_color'] ?? '#2563EB') ?>" onchange="document.getElementById('primaryColorHex').innerText = this.value">
                                    <span class="font-monospace text-xs fw-700 text-dark" id="primaryColorHex"><?= e($settings['primary_color'] ?? '#2563EB') ?></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-600 text-xs text-secondary">Header/Dark Accent</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" name="secondary_color" id="secColorInput" class="form-control form-control-color" value="<?= e($settings['secondary_color'] ?? '#0F172A') ?>" onchange="document.getElementById('secColorHex').innerText = this.value">
                                    <span class="font-monospace text-xs fw-700 text-dark" id="secColorHex"><?= e($settings['secondary_color'] ?? '#0F172A') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-800 shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Publish Theme & Store Design
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function selectThemeCard(card) {
    document.querySelectorAll('.theme-select-card').forEach(function(c) {
        c.classList.remove('border-primary', 'bg-primary-subtle', 'shadow');
        c.classList.add('border', 'bg-white', 'shadow-sm');
    });
    card.classList.remove('border', 'bg-white', 'shadow-sm');
    card.classList.add('border-primary', 'bg-primary-subtle', 'shadow');
    var radio = card.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
}
</script>

<?php View::endSection(); ?>
