<?php

use App\Core\View;
View::layout('layouts.auth');

View::section('title'); ?>Store Setup Wizard — BW Store<?php View::endSection();

View::section('content'); ?>

<div class="auth-card" style="max-width: 760px; width: 100%; border-radius: 18px;">
    <!-- Step Wizard Header -->
    <div class="text-center mb-4">
        <div class="logo-mark mx-auto mb-2">BW</div>
        <h3 class="fw-900 text-dark mb-1">Set Up Your Online Store</h3>
        <p class="text-secondary small">Follow these 5 easy steps to launch your digital storefront.</p>
        
        <!-- Step Indicators -->
        <div class="d-flex justify-content-center align-items-center gap-1 mt-3 flex-wrap">
            <span class="wizard-step-indicator active" id="pill-1">1. Business</span>
            <span class="text-secondary text-xs">&rsaquo;</span>
            <span class="wizard-step-indicator" id="pill-2">2. Store</span>
            <span class="text-secondary text-xs">&rsaquo;</span>
            <span class="wizard-step-indicator" id="pill-3">3. Theme</span>
            <span class="text-secondary text-xs">&rsaquo;</span>
            <span class="wizard-step-indicator" id="pill-4">4. Payments</span>
            <span class="text-secondary text-xs">&rsaquo;</span>
            <span class="wizard-step-indicator" id="pill-5">5. Plan & Launch</span>
        </div>
    </div>

    <form method="POST" action="<?= url('dashboard/onboarding') ?>" enctype="multipart/form-data" id="onboardingForm">
        <?= csrf_field() ?>

        <!-- STEP 1: Business Details -->
        <div class="wizard-step" id="step-1">
            <div class="card p-4 border mb-4 bg-white shadow-sm">
                <h5 class="fw-800 text-dark mb-3"><i class="bi bi-briefcase text-primary me-2"></i>Step 1: Your Business Profile</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-700 text-dark">Business / Brand Name <span class="text-danger">*</span></label>
                    <input type="text" name="business_name" class="form-control form-control-lg text-dark fw-600" placeholder="e.g. Royal Silk Boutique" value="<?= e($merchant['business_name'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-700 text-dark">Business Category <span class="text-danger">*</span></label>
                    <select name="business_category" class="form-select form-select-lg text-dark fw-600" required>
                        <option value="">Select Primary Category...</option>
                        <option value="Fashion & Apparel" <?= ($merchant['business_category'] ?? '') === 'Fashion & Apparel' ? 'selected' : '' ?>>Fashion & Apparel</option>
                        <option value="Electronics & Gadgets" <?= ($merchant['business_category'] ?? '') === 'Electronics & Gadgets' ? 'selected' : '' ?>>Electronics & Gadgets</option>
                        <option value="Jewelry & Accessories" <?= ($merchant['business_category'] ?? '') === 'Jewelry & Accessories' ? 'selected' : '' ?>>Jewelry & Accessories</option>
                        <option value="Health & Beauty" <?= ($merchant['business_category'] ?? '') === 'Health & Beauty' ? 'selected' : '' ?>>Health & Beauty</option>
                        <option value="Home & Living" <?= ($merchant['business_category'] ?? '') === 'Home & Living' ? 'selected' : '' ?>>Home & Living</option>
                        <option value="Food & Grocery" <?= ($merchant['business_category'] ?? '') === 'Food & Grocery' ? 'selected' : '' ?>>Food & Grocery</option>
                        <option value="Art & Crafts" <?= ($merchant['business_category'] ?? '') === 'Art & Crafts' ? 'selected' : '' ?>>Art & Crafts</option>
                        <option value="General Store" <?= ($merchant['business_category'] ?? '') === 'General Store' ? 'selected' : '' ?>>General Store</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary btn-lg px-4 fw-700" onclick="goToStep(2)">Continue to Store Details &rarr;</button>
            </div>
        </div>

        <!-- STEP 2: Store Name & Subdomain -->
        <div class="wizard-step d-none" id="step-2">
            <div class="card p-4 border mb-4 bg-white shadow-sm">
                <h5 class="fw-800 text-dark mb-3"><i class="bi bi-shop text-primary me-2"></i>Step 2: Store Name & Subdomain</h5>

                <div class="mb-3">
                    <label class="form-label fw-700 text-dark">Store Public Name <span class="text-danger">*</span></label>
                    <input type="text" name="store_name" id="store_name_input" class="form-control form-control-lg text-dark fw-600" placeholder="e.g. Royal Silk" value="<?= e($store['name'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-700 text-dark">Store Subdomain / Slug <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-700 text-dark">bwstore.in/store/</span>
                        <input type="text" name="store_slug" id="store_slug_input" class="form-control form-control-lg font-monospace text-dark fw-700" placeholder="royalsilk" value="<?= e($store['slug'] ?? '') ?>" required>
                    </div>
                    <div class="form-text text-xs text-primary mt-1">
                        <i class="bi bi-link-45deg me-1"></i> Live Store Link: <strong id="live_subdomain_preview"><?= url('store/' . ($store['slug'] ?? 'royalsilk')) ?></strong>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-700 text-dark">Store Brand Logo (Optional)</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    <div class="form-text text-xs text-secondary">Recommended size: 250x60px (PNG or SVG format).</div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 fw-600" onclick="goToStep(1)">&larr; Back</button>
                <button type="button" class="btn btn-primary btn-lg px-4 fw-700" onclick="goToStep(3)">Continue to Theme &rarr;</button>
            </div>
        </div>

        <!-- STEP 3: Theme Selection -->
        <div class="wizard-step d-none" id="step-3">
            <div class="card p-4 border mb-4 bg-white shadow-sm">
                <h5 class="fw-800 text-dark mb-3"><i class="bi bi-palette text-primary me-2"></i>Step 3: Choose Storefront Theme</h5>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="card p-3 border text-center cursor-pointer h-100 theme-select-card" style="cursor: pointer;">
                            <input type="radio" name="theme_name" value="modern" checked class="form-check-input mx-auto mb-2">
                            <h6 class="fw-800 text-dark mb-1">Modern Theme</h6>
                            <p class="text-secondary text-xs mb-0">Clean, crisp, high-speed layout for general retail.</p>
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label class="card p-3 border text-center cursor-pointer h-100 theme-select-card" style="cursor: pointer;">
                            <input type="radio" name="theme_name" value="fashion" class="form-check-input mx-auto mb-2">
                            <h6 class="fw-800 text-dark mb-1">Fashion Boutique</h6>
                            <p class="text-secondary text-xs mb-0">Luxury look, editorial typography for apparel & jewelry.</p>
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label class="card p-3 border text-center cursor-pointer h-100 theme-select-card" style="cursor: pointer;">
                            <input type="radio" name="theme_name" value="business" class="form-check-input mx-auto mb-2">
                            <h6 class="fw-800 text-dark mb-1">Business B2B</h6>
                            <p class="text-secondary text-xs mb-0">Structured catalogs, specifications & wholesale ready.</p>
                        </label>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-700 text-dark small">Brand Accent Color</label>
                        <input type="color" name="primary_color" class="form-control form-control-color w-100" value="#2563EB">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-700 text-dark small">WhatsApp Support Number</label>
                        <input type="text" name="whatsapp_number" class="form-control text-dark fw-600" placeholder="+91 98765 43210" value="<?= e($settings['whatsapp_number'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 fw-600" onclick="goToStep(2)">&larr; Back</button>
                <button type="button" class="btn btn-primary btn-lg px-4 fw-700" onclick="goToStep(4)">Continue to Customer Payments &rarr;</button>
            </div>
        </div>

        <!-- STEP 4: Customer Payments Setup -->
        <div class="wizard-step d-none" id="step-4">
            <div class="card p-4 border mb-4 bg-white shadow-sm">
                <h5 class="fw-800 text-dark mb-1"><i class="bi bi-wallet2 text-primary me-2"></i>Step 4: Customer Payment Options</h5>
                <p class="text-secondary small mb-3">Configure how your customers will pay you. Funds go 100% directly to your bank account.</p>

                <!-- COD Toggle -->
                <div class="border rounded-3 p-3 mb-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-800 text-dark mb-0"><i class="bi bi-cash-coin text-success me-1"></i> Cash on Delivery (COD)</h6>
                            <div class="text-secondary text-xs">Allow customers to pay with cash when package is delivered</div>
                        </div>
                        <div class="form-check form-switch fs-4">
                            <input class="form-check-input" type="checkbox" name="cod_enabled" value="1" id="codToggle" checked>
                        </div>
                    </div>
                </div>

                <!-- Merchant Razorpay Keys -->
                <div class="border rounded-3 p-3 mb-2">
                    <h6 class="fw-800 text-dark mb-1"><i class="bi bi-lightning-charge text-primary me-1"></i> Merchant Razorpay Account (Optional)</h6>
                    <p class="text-secondary text-xs mb-3">Enter your Razorpay Keys if you want instant Card / NetBanking payments to your bank.</p>

                    <div class="mb-2">
                        <label class="form-label text-xs fw-700 text-dark">Merchant Razorpay Key ID</label>
                        <input type="text" name="razorpay_key_id" class="form-control form-control-sm text-dark font-monospace" placeholder="rzp_live_xxxxxxxxxxxxxx" autocomplete="off">
                    </div>

                    <div class="mb-0">
                        <label class="form-label text-xs fw-700 text-dark">Merchant Razorpay Key Secret</label>
                        <input type="password" name="razorpay_key_secret" class="form-control form-control-sm text-dark font-monospace" placeholder="••••••••••••••••••••••••" autocomplete="new-password">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 fw-600" onclick="goToStep(3)">&larr; Back</button>
                <button type="button" class="btn btn-primary btn-lg px-4 fw-700" onclick="goToStep(5)">Choose Plan & Pay &rarr;</button>
            </div>
        </div>

        <!-- STEP 5: Choose SaaS Plan & Launch -->
        <div class="wizard-step d-none" id="step-5">
            <div class="card p-4 border mb-4 bg-white shadow-sm">
                <div class="text-center mb-3">
                    <div class="rounded-circle bg-primary text-white mx-auto d-flex align-items-center justify-content-center mb-2" style="width: 56px; height: 56px; font-size: 1.75rem;">
                        <i class="bi bi-stars"></i>
                    </div>
                    <h4 class="fw-900 text-dark mb-1">Select SaaS Package & Launch Store</h4>
                    <p class="text-secondary small">Your <strong>7-Day Free Trial</strong> is active. You can pay now or choose a plan for full access.</p>
                </div>

                <!-- Billing Cycle Selector -->
                <div class="text-center mb-3">
                    <div class="btn-group btn-group-sm p-1 bg-light rounded-pill border" role="group">
                        <input type="radio" class="btn-check" name="billing_cycle" id="cycleMonthly" value="monthly" checked onchange="toggleWizardCycle('monthly')">
                        <label class="btn btn-outline-primary rounded-pill px-3 fw-700" for="cycleMonthly">Monthly</label>

                        <input type="radio" class="btn-check" name="billing_cycle" id="cycleYearly" value="yearly" onchange="toggleWizardCycle('yearly')">
                        <label class="btn btn-outline-primary rounded-pill px-3 fw-700" for="cycleYearly">
                            Yearly <span class="badge bg-warning text-dark fw-800 text-xs ms-1">Save up to ₹1,000 Flat</span>
                        </label>
                    </div>
                </div>

                <!-- 3-Tier Plan Selection Cards -->
                <div class="row g-2 mb-4">
                    <!-- Starter -->
                    <div class="col-md-4">
                        <label class="card p-3 border text-center cursor-pointer h-100 plan-radio-card position-relative" style="cursor: pointer;" onclick="selectPlan(1, 499, 5888, 'BW Store Starter')">
                            <span class="badge bg-secondary text-white position-absolute top-0 start-50 translate-middle text-xs fw-800">STARTER</span>
                            <input type="radio" name="plan_id" value="1" id="plan_1" class="form-check-input mx-auto mb-2 mt-2">
                            <h6 class="fw-800 text-dark mb-1">Starter</h6>
                            <div class="fs-4 fw-900 text-primary mb-0 plan-price-display" id="planPrice_1">₹499</div>
                            <div class="text-secondary text-xs plan-period-display" id="planPeriod_1">/ 30 Days</div>
                            <div class="text-xs text-success fw-700 mt-1 yearly-discount-pill-1" style="display: none;">Save ₹100 Flat</div>
                            <div class="border-top pt-2 mt-2 text-xs text-secondary text-start">
                                <div><i class="bi bi-check-circle-fill text-success me-1"></i> Up to 10 Products</div>
                                <div><i class="bi bi-check-circle-fill text-success me-1"></i> 0% Sales Cut</div>
                            </div>
                        </label>
                    </div>

                    <!-- Growth (Recommended) -->
                    <div class="col-md-4">
                        <label class="card p-3 border-primary shadow-sm bg-primary-subtle text-center cursor-pointer h-100 plan-radio-card position-relative" style="cursor: pointer;" onclick="selectPlan(2, 999, 11788, 'BW Store Growth')">
                            <span class="badge bg-primary text-white position-absolute top-0 start-50 translate-middle text-xs fw-800">RECOMMENDED</span>
                            <input type="radio" name="plan_id" value="2" id="plan_2" checked class="form-check-input mx-auto mb-2 mt-2">
                            <h6 class="fw-800 text-dark mb-1">Growth</h6>
                            <div class="fs-4 fw-900 text-primary mb-0 plan-price-display" id="planPrice_2">₹999</div>
                            <div class="text-secondary text-xs plan-period-display" id="planPeriod_2">/ 30 Days</div>
                            <div class="text-xs text-success fw-700 mt-1 yearly-discount-pill-2" style="display: none;">Save ₹200 Flat</div>
                            <div class="border-top pt-2 mt-2 text-xs text-secondary text-start">
                                <div><i class="bi bi-check-circle-fill text-success me-1"></i> <strong>Unlimited Products</strong></div>
                                <div><i class="bi bi-check-circle-fill text-success me-1"></i> All 3 Themes</div>
                            </div>
                        </label>
                    </div>

                    <!-- Enterprise (VIP) -->
                    <div class="col-md-4">
                        <label class="card p-3 border text-center cursor-pointer h-100 plan-radio-card position-relative" style="cursor: pointer;" onclick="selectPlan(3, 2999, 34988, 'BW Store Enterprise')">
                            <span class="badge text-white position-absolute top-0 start-50 translate-middle text-xs fw-800" style="background:#7C3AED;">VIP BUSINESS</span>
                            <input type="radio" name="plan_id" value="3" id="plan_3" class="form-check-input mx-auto mb-2 mt-2">
                            <h6 class="fw-800 text-dark mb-1">Enterprise</h6>
                            <div class="fs-4 fw-900 text-primary mb-0 plan-price-display" id="planPrice_3">₹2,999</div>
                            <div class="text-secondary text-xs plan-period-display" id="planPeriod_3">/ 30 Days</div>
                            <div class="text-xs text-success fw-700 mt-1 yearly-discount-pill-3" style="display: none;">Save ₹1,000 Flat</div>
                            <div class="border-top pt-2 mt-2 text-xs text-secondary text-start">
                                <div><i class="bi bi-check-circle-fill text-success me-1"></i> Custom Domain Ready</div>
                                <div><i class="bi bi-check-circle-fill text-success me-1"></i> VIP 24/7 Support</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Payment Selection (UPI vs Razorpay) -->
                <div class="mb-3">
                    <label class="form-label fw-800 text-dark small mb-2">Select Payment Method:</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="card p-2 text-center border cursor-pointer h-100" style="cursor: pointer;" onclick="selectPaymentMethod('UPI')">
                                <input type="radio" name="payment_method" value="UPI" id="methodUpi" checked class="form-check-input mx-auto mb-1">
                                <div class="fw-800 text-dark small">UPI QR / Apps</div>
                                <div class="text-secondary text-xs">GPay, PhonePe, Paytm</div>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="card p-2 text-center border cursor-pointer h-100" style="cursor: pointer;" onclick="selectPaymentMethod('RAZORPAY')">
                                <input type="radio" name="payment_method" value="RAZORPAY" id="methodRzp" class="form-check-input mx-auto mb-1">
                                <div class="fw-800 text-dark small">Razorpay Gateway</div>
                                <div class="text-secondary text-xs">Cards / NetBanking</div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- UPI QR Scan Section -->
                <div id="upiSection" class="p-3 bg-light rounded-3 border text-center mb-3">
                    <p class="text-secondary small mb-2">Scan QR code using Google Pay, PhonePe, Paytm, or BHIM:</p>
                    <div class="mx-auto p-2 bg-white rounded border shadow-sm mb-2" style="width: 160px; height: 160px;">
                        <img id="upiQrImg" src="<?= $qrUrl ?>" alt="UPI QR Code" class="img-fluid" style="width: 100%; height: 100%;">
                    </div>
                    <div class="text-xs text-secondary mb-3">Pay exactly <strong class="text-dark fs-6" id="displayPlanAmount">₹999</strong> to UPI ID: <strong class="text-dark font-monospace"><?= e($adminUpi) ?></strong></div>
                    
                    <div class="text-start">
                        <label class="form-label text-xs fw-800 text-dark">Enter 12-Digit Bank UPI Reference / UTR Number <span class="text-danger">*</span></label>
                        <input type="text" name="utr_number" id="utrInput" class="form-control form-control-sm text-center font-monospace text-dark fw-700" placeholder="e.g. 423871928371">
                        <div class="text-secondary text-xs mt-1">
                            <i class="bi bi-info-circle text-primary me-1"></i> Shown on your payment receipt in GPay / PhonePe. Admin verifies in bank account.
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary px-4 fw-600" onclick="goToStep(4)">&larr; Back</button>
                <button type="submit" class="btn btn-success btn-lg px-4 py-3 fw-800 shadow" id="submitPaymentBtn">
                    <i class="bi bi-rocket-takeoff-fill me-2"></i> Pay <span id="submitPriceText">₹999</span> & Launch Store!
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.wizard-step-indicator {
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.35rem 0.65rem;
    border-radius: 20px;
    background: #e2e8f0;
    color: #475569;
    transition: all 0.2s ease;
}
.wizard-step-indicator.active {
    background: #2563EB;
    color: #ffffff;
}
.theme-select-card, .plan-radio-card {
    transition: all 0.2s ease;
}
.theme-select-card:hover, .plan-radio-card:hover {
    border-color: #2563EB;
    transform: translateY(-2px);
}
</style>

<script>
var currentPlanId = 2;
var currentMonthlyPrice = 999;
var currentYearlyPrice = 11788;
var currentCycle = 'monthly';
var currentPlanName = 'BW Store Growth';
var adminUpiId = "<?= e($adminUpi) ?>";
var merchantId = "<?= e($merchant['id'] ?? 1) ?>";

var planData = {
    1: { monthly: 499, yearly: 5888, name: 'BW Store Starter' },
    2: { monthly: 999, yearly: 11788, name: 'BW Store Growth' },
    3: { monthly: 2999, yearly: 34988, name: 'BW Store Enterprise' }
};

function goToStep(step) {
    for (let i = 1; i <= 5; i++) {
        document.getElementById('step-' + i).classList.add('d-none');
        document.getElementById('pill-' + i).classList.remove('active');
        if (i <= step) {
            document.getElementById('pill-' + i).classList.add('active');
        }
    }
    document.getElementById('step-' + step).classList.remove('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function toggleWizardCycle(cycle) {
    currentCycle = cycle;
    
    if (cycle === 'yearly') {
        document.getElementById('planPrice_1').innerText = '₹5,888';
        document.getElementById('planPeriod_1').innerText = '/ 1 Year';
        document.querySelector('.yearly-discount-pill-1').style.display = 'block';

        document.getElementById('planPrice_2').innerText = '₹11,788';
        document.getElementById('planPeriod_2').innerText = '/ 1 Year';
        document.querySelector('.yearly-discount-pill-2').style.display = 'block';

        document.getElementById('planPrice_3').innerText = '₹34,988';
        document.getElementById('planPeriod_3').innerText = '/ 1 Year';
        document.querySelector('.yearly-discount-pill-3').style.display = 'block';
    } else {
        document.getElementById('planPrice_1').innerText = '₹499';
        document.getElementById('planPeriod_1').innerText = '/ 30 Days';
        document.querySelector('.yearly-discount-pill-1').style.display = 'none';

        document.getElementById('planPrice_2').innerText = '₹999';
        document.getElementById('planPeriod_2').innerText = '/ 30 Days';
        document.querySelector('.yearly-discount-pill-2').style.display = 'none';

        document.getElementById('planPrice_3').innerText = '₹2,999';
        document.getElementById('planPeriod_3').innerText = '/ 30 Days';
        document.querySelector('.yearly-discount-pill-3').style.display = 'none';
    }

    updatePriceAndQr();
}

function selectPlan(planId, monthlyPrice, yearlyPrice, name) {
    currentPlanId = planId;
    currentMonthlyPrice = monthlyPrice;
    currentYearlyPrice = yearlyPrice;
    currentPlanName = name;

    document.querySelectorAll('.plan-radio-card').forEach(function(el) {
        el.classList.remove('border-primary', 'shadow-sm', 'bg-primary-subtle');
    });
    var selectedRadio = document.getElementById('plan_' + planId);
    if (selectedRadio) {
        selectedRadio.checked = true;
        selectedRadio.closest('.plan-radio-card').classList.add('border-primary', 'shadow-sm', 'bg-primary-subtle');
    }

    updatePriceAndQr();
}

function updatePriceAndQr() {
    var price = (currentCycle === 'yearly') ? planData[currentPlanId].yearly : planData[currentPlanId].monthly;
    var periodLabel = (currentCycle === 'yearly') ? '1 Year' : '30 Days';

    document.getElementById('displayPlanAmount').innerText = '₹' + price.toLocaleString('en-IN');
    document.getElementById('submitPriceText').innerText = '₹' + price.toLocaleString('en-IN') + ' (' + periodLabel + ')';

    var upiLink = "upi://pay?pa=" + encodeURIComponent(adminUpiId) + "&pn=" + encodeURIComponent("BW Store SaaS") + "&am=" + price.toFixed(2) + "&cu=INR&tn=" + encodeURIComponent("Setup #M-" + merchantId + " " + currentPlanName + " (" + periodLabel + ")");
    var qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=" + encodeURIComponent(upiLink);
    document.getElementById('upiQrImg').src = qrUrl;
}

function selectPaymentMethod(method) {
    if (method === 'UPI') {
        document.getElementById('methodUpi').checked = true;
        document.getElementById('upiSection').classList.remove('d-none');
    } else {
        document.getElementById('methodRzp').checked = true;
        document.getElementById('upiSection').classList.add('d-none');
    }
}

document.getElementById('store_name_input').addEventListener('input', function() {
    var slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    if (!slug) slug = 'mystore';
    document.getElementById('store_slug_input').value = slug;
    document.getElementById('live_subdomain_preview').innerText = '<?= url("store") ?>/' + slug;
});

document.getElementById('store_slug_input').addEventListener('input', function() {
    var slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    document.getElementById('live_subdomain_preview').innerText = '<?= url("store") ?>/' + slug;
});
</script>

<?php View::endSection(); ?>
