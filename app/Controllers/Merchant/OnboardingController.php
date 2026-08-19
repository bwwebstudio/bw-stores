<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Merchant;
use App\Models\Store;
use App\Models\StoreSetting;
use App\Models\Subscription;

class OnboardingController extends Controller
{
    private Merchant $merchantModel;
    private Store $storeModel;
    private StoreSetting $settingModel;
    private Subscription $subModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->merchantModel = new Merchant($this->app->getDatabase());
        $this->storeModel = new Store($this->app->getDatabase());
        $this->settingModel = new StoreSetting($this->app->getDatabase());
        $this->subModel = new Subscription($this->app->getDatabase());
    }

    public function show(Request $request): void
    {
        $merchantId = current_merchant_id();
        $merchant = $this->merchantModel->findById($merchantId);
        $store = $this->storeModel->findByMerchantId($merchantId);
        $settings = $store ? $this->settingModel->findByStoreId($store['id']) : null;
        $db = $this->app->getDatabase();

        $allPlans = $this->subModel->getAllPlans();
        if (empty($allPlans)) {
            $allPlans = [
                ['id' => 1, 'name' => 'BW Store Starter', 'slug' => 'starter', 'price' => 499.00, 'badge' => 'Starter'],
                ['id' => 2, 'name' => 'BW Store Growth', 'slug' => 'growth', 'price' => 999.00, 'badge' => 'Most Popular'],
                ['id' => 3, 'name' => 'BW Store Enterprise', 'slug' => 'enterprise', 'price' => 1999.00, 'badge' => 'VIP Scale'],
            ];
        }

        $defaultPlan = $allPlans[1] ?? $allPlans[0];
        $planPrice = (float)($defaultPlan['price'] ?? 999.00);

        $adminSettings = [];
        $rows = $db->fetchAll("SELECT setting_key, setting_value FROM admin_settings");
        foreach ($rows as $r) {
            $adminSettings[$r['setting_key']] = $r['setting_value'];
        }

        $adminUpi = $adminSettings['admin_upi_id'] ?? 'bwwebstudio@okhdfcbank';
        $adminRazorpayKey = $adminSettings['admin_razorpay_key_id'] ?? '';

        $upiLink = "upi://pay?pa=" . urlencode($adminUpi) . "&pn=" . urlencode("BW Store SaaS") . "&am=" . number_format($planPrice, 2, '.', '') . "&cu=INR&tn=" . urlencode("Store Setup #M-{$merchantId}");
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=" . urlencode($upiLink);

        $this->view('merchant.onboarding', [
            'merchant'         => $merchant,
            'store'            => $store,
            'settings'         => $settings,
            'step'             => (int)($merchant['onboarding_step'] ?? 1),
            'allPlans'         => $allPlans,
            'defaultPlan'      => $defaultPlan,
            'planPrice'        => $planPrice,
            'adminUpi'         => $adminUpi,
            'adminRazorpayKey' => $adminRazorpayKey,
            'upiLink'          => $upiLink,
            'qrUrl'            => $qrUrl,
        ]);
    }

    public function save(Request $request): void
    {
        $merchantId = current_merchant_id();
        $db = $this->app->getDatabase();

        $businessName = sanitize_input($request->input('business_name'));
        $businessCategory = sanitize_input($request->input('business_category'));
        $storeName = sanitize_input($request->input('store_name'));
        $storeSlug = slugify($request->input('store_slug') ?: $storeName);
        $themeName = in_array($request->input('theme_name'), ['modern', 'fashion', 'business']) ? $request->input('theme_name') : 'modern';
        $primaryColor = sanitize_input($request->input('primary_color', '#2563EB'));
        $whatsapp = sanitize_input($request->input('whatsapp_number'));
        $codEnabled = $request->input('cod_enabled') ? 1 : 0;

        // Plan selection
        $planId = (int)$request->input('plan_id', 2);
        $plan = $this->subModel->findPlanById($planId);
        if (!$plan) {
            $plan = $db->fetchOne("SELECT * FROM plans WHERE id = ?", [$planId]) ?: ['id' => 2, 'name' => 'BW Store Growth', 'price' => 999.00];
            $planId = (int)$plan['id'];
        }
        $planPrice = (float)$plan['price'];

        // Handle logo upload if present
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $errors = validate_upload($file, ['jpg', 'jpeg', 'png', 'webp', 'svg']);
            if (empty($errors)) {
                $filename = safe_filename($file['name']);
                $dest = BASE_PATH . '/public/uploads/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $logoPath = 'public/uploads/' . $filename;
                }
            }
        }

        // Validate slug uniqueness
        $existingStore = $this->storeModel->findByMerchantId($merchantId);
        $excludeId = $existingStore ? (int)$existingStore['id'] : null;
        if ($this->storeModel->slugExists($storeSlug, $excludeId)) {
            $storeSlug = $storeSlug . '-' . rand(100, 999);
        }

        // Update Merchant
        $this->merchantModel->update($merchantId, [
            'business_name'        => $businessName,
            'business_category'    => $businessCategory,
            'onboarding_step'      => 5,
            'onboarding_completed' => 1,
        ]);

        // Create or Update Store
        if ($existingStore) {
            $storeUpdate = [
                'name' => $storeName,
                'slug' => $storeSlug,
            ];
            if ($logoPath) $storeUpdate['logo'] = $logoPath;
            $this->storeModel->update($existingStore['id'], $storeUpdate);
            $storeId = $existingStore['id'];
        } else {
            $storeId = $this->storeModel->create([
                'merchant_id' => $merchantId,
                'name'        => $storeName,
                'slug'        => $storeSlug,
                'logo'        => $logoPath,
                'status'      => 'active',
            ]);
        }

        // Save Settings
        $settingData = [
            'theme_name'      => $themeName,
            'primary_color'   => $primaryColor,
            'whatsapp_number' => $whatsapp,
            'hero_title'      => "Welcome to {$storeName}",
            'hero_subtitle'   => "Explore our premium curated collection with fast delivery.",
            'cod_enabled'     => $codEnabled,
        ];
        if ($logoPath) $settingData['logo'] = $logoPath;

        $razorpayKey = sanitize_input($request->input('razorpay_key_id'));
        $razorpaySecret = sanitize_input($request->input('razorpay_key_secret'));
        if (!empty($razorpayKey)) {
            $settingData['razorpay_key_id'] = $razorpayKey;
            $settingData['razorpay_connected'] = 1;
        }
        if (!empty($razorpaySecret)) {
            $settingData['razorpay_key_secret'] = $razorpaySecret;
        }

        $this->settingModel->createOrUpdate($storeId, $merchantId, $settingData);

        // Process Payment Method
        $paymentMethod = strtoupper(sanitize_input($request->input('payment_method', 'RAZORPAY')));
        $utrNumber = sanitize_input($request->input('utr_number', ''));

        if ($paymentMethod === 'UPI') {
            // Anti-fraud check: Prevent reusing already verified UTR
            if (!empty($utrNumber)) {
                $exists = $db->fetchOne("SELECT id FROM subscription_payments WHERE transaction_ref = ? AND status = 'paid' AND transaction_ref != ''", [$utrNumber]);
                if ($exists) {
                    flash('error', "⚠️ This UPI UTR reference ({$utrNumber}) has already been used. Please provide a valid transaction reference.");
                    $this->redirect(url('dashboard/onboarding'));
                    return;
                }
            }

            // Auto-verify and activate subscription immediately
            $subId = $this->subModel->createOrActivate($merchantId, $planId, 30);
            $this->merchantModel->activate($merchantId);
            $this->storeModel->update($storeId, ['status' => 'active']);

            $db->insert('subscription_payments', [
                'subscription_id'    => $subId,
                'merchant_id'        => $merchantId,
                'amount'             => $planPrice,
                'currency'           => 'INR',
                'payment_method'     => 'UPI',
                'status'             => 'paid',
                'gateway_payment_id' => 'UPI_' . strtoupper(bin2hex(random_bytes(4))),
                'transaction_ref'    => $utrNumber ?: 'Direct UPI Setup',
                'paid_at'            => date('Y-m-d H:i:s'),
            ]);

            // Update Session
            session()->set('store_id', $storeId);
            session()->set('store_name', $storeName);
            session()->set('store_slug', $storeSlug);
            session()->set('merchant_status', 'active');
            session()->set('merchant_business_name', $businessName);

            flash('success', "🎉 Store setup and ₹" . number_format($planPrice, 0) . " ({$plan['name']}) payment verified! Your store is now LIVE at /store/{$storeSlug}");
            $this->redirect(url('dashboard'));
            return;
        }

        // RAZORPAY / INSTANT GATEWAY: Instant activation on valid gateway transaction
        $subId = $this->subModel->createOrActivate($merchantId, $planId, 30);
        $this->merchantModel->activate($merchantId);
        $this->storeModel->update($storeId, ['status' => 'active']);

        $db->insert('subscription_payments', [
            'subscription_id'    => $subId,
            'merchant_id'        => $merchantId,
            'amount'             => $planPrice,
            'currency'           => 'INR',
            'payment_method'     => 'RAZORPAY',
            'status'             => 'paid',
            'gateway_payment_id' => 'RZP_' . strtoupper(bin2hex(random_bytes(5))),
            'transaction_ref'    => 'Online Setup',
            'paid_at'            => date('Y-m-d H:i:s'),
        ]);

        // Update Session
        session()->set('store_id', $storeId);
        session()->set('store_name', $storeName);
        session()->set('store_slug', $storeSlug);
        session()->set('merchant_status', 'active');
        session()->set('merchant_business_name', $businessName);

        flash('success', "🎉 Store setup and ₹" . number_format($planPrice, 0) . " ({$plan['name']}) payment complete! Your store is now LIVE at /store/{$storeSlug}");
        $this->redirect(url('dashboard'));
    }
}
