<?php

use App\Core\View;
View::layout('layouts.auth');

View::section('title'); ?>SaaS Subscription Payment — BW Store<?php View::endSection();

View::section('content'); ?>

<div class="auth-card" style="max-width: 720px; width: 100%; border-radius: 18px;">
    <div class="text-center mb-4">
        <div class="logo-mark mx-auto mb-2">BW</div>
        <h3 class="fw-900 text-dark mb-1">Activate / Renew Store Subscription</h3>
        <p class="text-secondary small">Choose your SaaS package and billing cycle to get 30 days or 1 year full store access.</p>
    </div>

    <!-- Monthly / Yearly Switch Toggle -->
    <div class="text-center mb-3">
        <div class="billing-toggle-wrapper">
            <button type="button" class="billing-toggle-btn <?= ($cycle ?? 'monthly') === 'monthly' ? 'active' : '' ?>" id="checkoutBtnMonthly" onclick="toggleCheckoutCycle('monthly')">Monthly Billing</button>
            <button type="button" class="billing-toggle-btn <?= ($cycle ?? 'monthly') === 'yearly' ? 'active' : '' ?>" id="checkoutBtnYearly" onclick="toggleCheckoutCycle('yearly')">
                Yearly Billing <span class="badge bg-warning text-dark fw-800 text-xs ms-1">Save up to ₹1,000 Flat</span>
            </button>
        </div>
    </div>

    <!-- Plan Selection Cards -->
    <div class="row g-2 mb-4">
        <!-- Starter (₹499) -->
        <div class="col-4">
            <div class="card p-2 text-center border cursor-pointer h-100 plan-picker-card <?= ($selectedPlan['id'] ?? 2) == 1 ? 'border-primary shadow-sm bg-primary-subtle' : '' ?>" style="cursor: pointer;" onclick="changeCheckoutPlan(1, 499, 5888, 'BW Store Starter', 'Starter')">
                <span class="badge bg-secondary text-white text-xs mb-1">STARTER</span>
                <h6 class="fw-800 text-dark mb-0 text-xs">Starter</h6>
                <div class="fs-5 fw-900 text-primary mt-1" id="cardPrice_1">₹<?= ($cycle === 'yearly') ? '5,888' : '499' ?></div>
                <div class="text-secondary text-xs" id="cardPeriod_1"><?= ($cycle === 'yearly') ? '/ 1 Year' : '/ 30 Days' ?></div>
            </div>
        </div>

        <!-- Growth (₹999 - Recommended) -->
        <div class="col-4">
            <div class="card p-2 text-center border cursor-pointer h-100 plan-picker-card <?= ($selectedPlan['id'] ?? 2) == 2 ? 'border-primary shadow-sm bg-primary-subtle' : '' ?>" style="cursor: pointer;" onclick="changeCheckoutPlan(2, 999, 11788, 'BW Store Growth', 'Recommended')">
                <span class="badge bg-primary text-white text-xs mb-1">RECOMMENDED</span>
                <h6 class="fw-800 text-dark mb-0 text-xs">Growth</h6>
                <div class="fs-5 fw-900 text-primary mt-1" id="cardPrice_2">₹<?= ($cycle === 'yearly') ? '11,788' : '999' ?></div>
                <div class="text-secondary text-xs" id="cardPeriod_2"><?= ($cycle === 'yearly') ? '/ 1 Year' : '/ 30 Days' ?></div>
            </div>
        </div>

        <!-- Enterprise (₹2,999 - VIP) -->
        <div class="col-4">
            <div class="card p-2 text-center border cursor-pointer h-100 plan-picker-card <?= ($selectedPlan['id'] ?? 2) == 3 ? 'border-primary shadow-sm bg-primary-subtle' : '' ?>" style="cursor: pointer;" onclick="changeCheckoutPlan(3, 2999, 34988, 'BW Store Enterprise', 'VIP Business')">
                <span class="badge text-white text-xs mb-1" style="background:#7C3AED;">VIP BUSINESS</span>
                <h6 class="fw-800 text-dark mb-0 text-xs">Enterprise</h6>
                <div class="fs-5 fw-900 text-primary mt-1" id="cardPrice_3">₹<?= ($cycle === 'yearly') ? '34,988' : '2,999' ?></div>
                <div class="text-secondary text-xs" id="cardPeriod_3"><?= ($cycle === 'yearly') ? '/ 1 Year' : '/ 30 Days' ?></div>
            </div>
        </div>
    </div>

    <!-- Selected Plan Summary Card -->
    <div class="card p-3 border mb-4 shadow-sm" style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); color: #fff; border-radius: 14px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <span class="badge bg-primary px-2 py-1 text-xs fw-800" id="summaryPlanBadge"><?= e($selectedPlan['badge'] ?? 'RECOMMENDED') ?></span>
                <h5 class="fw-800 mb-0 text-white mt-1" id="summaryPlanName"><?= e($selectedPlan['name'] ?? 'BW Store Growth') ?></h5>
            </div>
            <div class="text-end">
                <span class="fs-3 fw-900 text-white" id="summaryPlanPrice">₹<?= number_format($planPrice, 0) ?></span>
                <span class="text-light text-xs d-block opacity-75" id="summaryPeriodLabel"><?= ($cycle === 'yearly') ? '/ 1 Year' : '/ 30 Days' ?></span>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-3 text-xs text-light border-top border-secondary pt-2 mt-1">
            <span><i class="bi bi-check-circle-fill text-success me-1"></i> 0% Sales Commission</span>
            <span id="summaryProductLimit">
                <i class="bi bi-check-circle-fill text-success me-1"></i> 
                <?= ($selectedPlan['id'] ?? 2) == 1 ? 'Up to 10 Products' : 'Unlimited Products' ?>
            </span>
            <span><i class="bi bi-check-circle-fill text-success me-1"></i> Live Storefront Hosting</span>
        </div>
    </div>

    <!-- Payment Methods Tab System -->
    <ul class="nav nav-pills nav-fill mb-3 p-1 bg-light rounded-3 border" id="paymentTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-800 py-2" id="upi-tab" data-bs-toggle="tab" data-bs-target="#upi-pane" type="button" role="tab">
                <i class="bi bi-qr-code-scan me-1"></i> UPI QR & Apps
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-800 py-2" id="razorpay-tab" data-bs-toggle="tab" data-bs-target="#razorpay-pane" type="button" role="tab">
                <i class="bi bi-credit-card-2-front me-1"></i> Razorpay Gateway
            </button>
        </li>
    </ul>

    <div class="tab-content" id="paymentTabsContent">
        <!-- TAB 1: UPI Direct / QR Scan -->
        <div class="tab-pane fade show active" id="upi-pane" role="tabpanel">
            <div class="card p-4 border text-center bg-white shadow-sm mb-3">
                <p class="text-secondary small mb-3">Scan this QR Code using <strong>Google Pay, PhonePe, Paytm, or BHIM</strong>:</p>

                <div class="mx-auto p-2 bg-white rounded border shadow-sm mb-3" style="width: 200px; height: 200px;">
                    <img id="checkoutQrImg" src="<?= $qrUrl ?>" alt="UPI QR Code" class="img-fluid rounded" style="width: 100%; height: 100%;">
                </div>

                <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
                    <span class="text-secondary small fw-600">UPI ID:</span>
                    <code class="fw-800 text-dark fs-6 font-monospace" id="upiVpaText"><?= e($adminUpi) ?></code>
                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="copyUpiId()" id="copyUpiBtn">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>

                <!-- Mobile Direct App Link -->
                <div class="d-md-none mb-3">
                    <a id="mobileUpiLink" href="<?= $upiLink ?>" class="btn btn-outline-success w-100 fw-700 py-2">
                        <i class="bi bi-phone me-1"></i> Open Google Pay / PhonePe App
                    </a>
                </div>

                <form method="POST" action="<?= url('dashboard/subscription/pay') ?>" data-loading>
                    <?= csrf_field() ?>
                    <input type="hidden" name="payment_method" value="UPI">
                    <input type="hidden" name="plan_id" id="upiPlanId" value="<?= (int)($selectedPlan['id'] ?? 2) ?>">
                    <input type="hidden" name="billing_cycle" id="upiBillingCycle" value="<?= e($cycle ?? 'monthly') ?>">

                    <div class="mb-3 text-start">
                        <label class="form-label fw-700 text-xs text-dark">12-Digit Bank UPI Reference / UTR Number <span class="text-danger">*</span></label>
                        <input type="text" name="transaction_id" class="form-control text-center font-monospace text-dark fw-700" placeholder="e.g. 423871928371" required>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-800 shadow" id="upiSubmitBtn">
                        <i class="bi bi-check-circle-fill me-2"></i> Submit Payment (<span id="upiBtnAmount">₹<?= number_format($planPrice, 0) ?></span>)
                    </button>
                </form>
            </div>
        </div>

        <!-- TAB 2: Razorpay Gateway Checkout -->
        <div class="tab-pane fade" id="razorpay-pane" role="tabpanel">
            <div class="card p-4 border text-center bg-white shadow-sm mb-3">
                <div class="rounded-circle bg-primary-subtle text-primary mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; font-size: 1.75rem;">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h5 class="fw-800 text-dark mb-1">Instant Online Checkout</h5>
                <p class="text-secondary small mb-4">Pay securely using Credit/Debit Card, NetBanking, or Wallet via Razorpay Gateway.</p>

                <form method="POST" action="<?= url('dashboard/subscription/pay') ?>" data-loading>
                    <?= csrf_field() ?>
                    <input type="hidden" name="payment_method" value="RAZORPAY">
                    <input type="hidden" name="plan_id" id="rzpPlanId" value="<?= (int)($selectedPlan['id'] ?? 2) ?>">
                    <input type="hidden" name="billing_cycle" id="rzpBillingCycle" value="<?= e($cycle ?? 'monthly') ?>">
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-800 shadow" id="rzpSubmitBtn">
                        <i class="bi bi-lightning-charge-fill me-2"></i> Pay <span id="rzpBtnAmount">₹<?= number_format($planPrice, 0) ?></span> via Gateway
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <a href="<?= url('dashboard/subscription') ?>" class="text-primary text-xs fw-700 text-decoration-none">
            &larr; Back to Subscriptions
        </a>
        <span class="text-secondary text-xs fw-600">
            <i class="bi bi-lock-fill text-success me-1"></i> 256-Bit SSL Encrypted
        </span>
    </div>
</div>

<script>
var adminUpi = "<?= e($adminUpi) ?>";
var merchantId = "<?= current_merchant_id() ?>";
var currentPlanId = <?= (int)($selectedPlan['id'] ?? 2) ?>;
var currentCycle = "<?= e($cycle ?? 'monthly') ?>";

var planData = {
    1: { monthly: 499, yearly: 5888, name: 'BW Store Starter', badge: 'STARTER', maxProducts: 'Up to 10 Products' },
    2: { monthly: 999, yearly: 11788, name: 'BW Store Growth', badge: 'RECOMMENDED', maxProducts: 'Unlimited Products' },
    3: { monthly: 2999, yearly: 34988, name: 'BW Store Enterprise', badge: 'VIP BUSINESS', maxProducts: 'Unlimited Products' }
};

function toggleCheckoutCycle(cycle) {
    currentCycle = cycle;
    var btnM = document.getElementById('checkoutBtnMonthly');
    var btnY = document.getElementById('checkoutBtnYearly');

    if (cycle === 'yearly') {
        btnY.classList.add('active');
        btnM.classList.remove('active');

        document.getElementById('cardPrice_1').innerText = '₹5,888';
        document.getElementById('cardPeriod_1').innerText = '/ 1 Year';
        document.getElementById('cardPrice_2').innerText = '₹11,788';
        document.getElementById('cardPeriod_2').innerText = '/ 1 Year';
        document.getElementById('cardPrice_3').innerText = '₹34,988';
        document.getElementById('cardPeriod_3').innerText = '/ 1 Year';
    } else {
        btnM.classList.add('active');
        btnY.classList.remove('active');

        document.getElementById('cardPrice_1').innerText = '₹499';
        document.getElementById('cardPeriod_1').innerText = '/ 30 Days';
        document.getElementById('cardPrice_2').innerText = '₹999';
        document.getElementById('cardPeriod_2').innerText = '/ 30 Days';
        document.getElementById('cardPrice_3').innerText = '₹2,999';
        document.getElementById('cardPeriod_3').innerText = '/ 30 Days';
    }

    document.getElementById('upiBillingCycle').value = cycle;
    document.getElementById('rzpBillingCycle').value = cycle;

    updateCheckoutSummary();
}

function changeCheckoutPlan(planId, monthlyPrice, yearlyPrice, name, badge) {
    currentPlanId = planId;
    document.querySelectorAll('.plan-picker-card').forEach(function(c) {
        c.classList.remove('border-primary', 'shadow-sm', 'bg-primary-subtle');
    });
    event.currentTarget.classList.add('border-primary', 'shadow-sm', 'bg-primary-subtle');

    document.getElementById('upiPlanId').value = planId;
    document.getElementById('rzpPlanId').value = planId;

    updateCheckoutSummary();
}

function updateCheckoutSummary() {
    var p = planData[currentPlanId];
    var price = (currentCycle === 'yearly') ? p.yearly : p.monthly;
    var periodLabel = (currentCycle === 'yearly') ? '/ 1 Year' : '/ 30 Days';
    var periodText = (currentCycle === 'yearly') ? '1 Year' : '30 Days';

    document.getElementById('summaryPlanName').innerText = p.name;
    document.getElementById('summaryPlanBadge').innerText = p.badge;
    document.getElementById('summaryPlanPrice').innerText = '₹' + price.toLocaleString('en-IN');
    document.getElementById('summaryPeriodLabel').innerText = periodLabel;
    document.getElementById('summaryProductLimit').innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i> ' + p.maxProducts;

    document.getElementById('upiBtnAmount').innerText = '₹' + price.toLocaleString('en-IN');
    document.getElementById('rzpBtnAmount').innerText = '₹' + price.toLocaleString('en-IN');

    var upiLink = "upi://pay?pa=" + encodeURIComponent(adminUpi) + "&pn=" + encodeURIComponent("BW Store SaaS") + "&am=" + price.toFixed(2) + "&cu=INR&tn=" + encodeURIComponent("SaaS #M-" + merchantId + " " + p.name + " (" + periodText + ")");
    var qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=" + encodeURIComponent(upiLink);

    document.getElementById('checkoutQrImg').src = qrUrl;
    var mobLink = document.getElementById('mobileUpiLink');
    if (mobLink) mobLink.href = upiLink;
}

function copyUpiId() {
    var vpa = "<?= e($adminUpi) ?>";
    navigator.clipboard.writeText(vpa).then(function() {
        var btn = document.getElementById('copyUpiBtn');
        btn.innerHTML = '<i class="bi bi-check-lg text-success"></i>';
        setTimeout(function() {
            btn.innerHTML = '<i class="bi bi-clipboard"></i>';
        }, 2000);
    });
}
</script>

<?php View::endSection(); ?>
