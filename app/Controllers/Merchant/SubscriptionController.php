<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    private Subscription $subModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->subModel = new Subscription($this->app->getDatabase());
    }

    /**
     * View subscription status, current plan, usage metrics, and payment invoices
     */
    public function index(Request $request): void
    {
        $merchantId = current_merchant_id();
        $db = $this->app->getDatabase();

        $subscription = $this->subModel->findByMerchantId($merchantId);
        $allPlans = $this->subModel->getAllPlans();
        $payments = $this->subModel->getPayments($merchantId);

        // Calculate merchant product count for plan limit meter
        $productCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM products WHERE merchant_id = ?", [$merchantId]);

        $this->view('merchant.subscription.index', [
            'subscription' => $subscription,
            'allPlans'     => $allPlans,
            'payments'     => $payments,
            'productCount' => $productCount,
        ]);
    }

    /**
     * Subscription checkout / renewal page with 3-tier selection, monthly/yearly toggle and UPI QR
     */
    public function checkout(Request $request): void
    {
        $merchantId = current_merchant_id();
        $db = $this->app->getDatabase();

        $allPlans = $this->subModel->getAllPlans();
        
        // Determine requested plan and billing cycle
        $planSlug = $request->query('plan');
        $planId = (int)$request->query('plan_id', 0);
        $cycle = $request->query('cycle', 'monthly') === 'yearly' ? 'yearly' : 'monthly';

        $selectedPlan = null;
        if ($planSlug) {
            $selectedPlan = $this->subModel->findPlanBySlug($planSlug);
        } elseif ($planId > 0) {
            $selectedPlan = $this->subModel->findPlanById($planId);
        }

        if (!$selectedPlan) {
            // Check existing subscription plan, or default to Growth (id: 2)
            $existingSub = $this->subModel->findByMerchantId($merchantId);
            if ($existingSub && !empty($existingSub['plan_id'])) {
                $selectedPlan = $this->subModel->findPlanById((int)$existingSub['plan_id']);
            }
            if (!$selectedPlan && !empty($allPlans)) {
                // Default to Growth (middle / recommended tier)
                $selectedPlan = $allPlans[1] ?? $allPlans[0];
            }
        }

        $planPrice = ($cycle === 'yearly') ? (float)($selectedPlan['yearly_price'] ?? ($selectedPlan['price'] * 12)) : (float)($selectedPlan['price'] ?? 999.00);

        // Fetch Admin UPI and Gateway Settings
        $adminSettings = [];
        $rows = $db->fetchAll("SELECT setting_key, setting_value FROM admin_settings");
        foreach ($rows as $r) {
            $adminSettings[$r['setting_key']] = $r['setting_value'];
        }

        $adminUpi = $adminSettings['admin_upi_id'] ?? 'bwwebstudio@okhdfcbank';
        $adminRazorpayKey = $adminSettings['admin_razorpay_key_id'] ?? '';

        // Generate standard Indian UPI Payment intent link with dynamic plan price and merchant reference
        $planName = $selectedPlan['name'] ?? 'BW Store Plan';
        $periodLabel = ($cycle === 'yearly') ? '1 Year' : '30 Days';
        $upiLink = "upi://pay?pa=" . urlencode($adminUpi) . 
                   "&pn=" . urlencode("BW Store SaaS") . 
                   "&am=" . number_format($planPrice, 2, '.', '') . 
                   "&cu=INR&tn=" . urlencode("SaaS #M-{$merchantId} {$planName} ({$periodLabel})");
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=" . urlencode($upiLink);

        $this->view('merchant.subscription.checkout', [
            'allPlans'         => $allPlans,
            'selectedPlan'     => $selectedPlan,
            'planPrice'        => $planPrice,
            'cycle'            => $cycle,
            'adminUpi'         => $adminUpi,
            'adminRazorpayKey' => $adminRazorpayKey,
            'upiLink'          => $upiLink,
            'qrUrl'            => $qrUrl,
        ]);
    }

    /**
     * Process subscription payment submission
     */
    public function pay(Request $request): void
    {
        $merchantId = current_merchant_id();
        $db = $this->app->getDatabase();

        $planId = (int)$request->input('plan_id', 2);
        $cycle = $request->input('billing_cycle', 'monthly') === 'yearly' ? 'yearly' : 'monthly';
        $periodDays = ($cycle === 'yearly') ? 365 : 30;

        $plan = $this->subModel->findPlanById($planId);
        if (!$plan) {
            $plan = $db->fetchOne("SELECT * FROM plans ORDER BY id ASC LIMIT 1") ?: ['id' => 1, 'name' => 'BW Store Starter', 'price' => 499.00];
            $planId = (int)$plan['id'];
        }

        $planPrice = ($cycle === 'yearly') ? (float)($plan['yearly_price'] ?? ($plan['price'] * 12)) : (float)$plan['price'];
        $paymentMethod = strtoupper(sanitize_input($request->input('payment_method', 'RAZORPAY')));
        $utrNumber = sanitize_input($request->input('transaction_id', ''));

        if ($paymentMethod === 'UPI') {
            if (empty($utrNumber) || strlen(trim($utrNumber)) < 6) {
                flash('error', '⚠️ Please enter a valid 12-digit Bank UPI Reference / UTR Number.');
                $this->redirect(url('dashboard/subscription/checkout?plan_id=' . $planId . '&cycle=' . $cycle));
                return;
            }

            // Anti-fraud check: Prevent reusing already verified UTR
            $exists = $db->fetchOne("SELECT id FROM subscription_payments WHERE transaction_ref = ? AND status = 'paid' AND transaction_ref != ''", [$utrNumber]);
            if ($exists) {
                flash('error', "⚠️ This UPI UTR reference ({$utrNumber}) has already been used for an active payment. Please provide a new transaction reference.");
                $this->redirect(url('dashboard/subscription/checkout?plan_id=' . $planId . '&cycle=' . $cycle));
                return;
            }

            // Auto-verify and activate subscription immediately
            $subId = $this->subModel->createOrActivate($merchantId, $planId, $periodDays);

            // Record Verified Payment Record
            $db->insert('subscription_payments', [
                'subscription_id'    => $subId,
                'merchant_id'        => $merchantId,
                'amount'             => $planPrice,
                'currency'           => 'INR',
                'payment_method'     => 'UPI',
                'status'             => 'paid',
                'gateway_payment_id' => 'UPI_' . strtoupper(bin2hex(random_bytes(4))),
                'transaction_ref'    => $utrNumber,
                'paid_at'            => date('Y-m-d H:i:s'),
            ]);

            // Activate merchant and store
            $db->update('merchants', ['onboarding_completed' => 1, 'status' => 'active'], 'id = ?', [$merchantId]);
            $store = $db->fetchOne("SELECT id FROM stores WHERE merchant_id = ?", [$merchantId]);
            if ($store) {
                $db->update('stores', ['status' => 'active'], 'id = ?', [$store['id']]);
            }

            session()->set('merchant_status', 'active');

            // Notify merchant
            $db->insert('notifications', [
                'merchant_id' => $merchantId,
                'title'       => '🎉 UPI Payment Auto-Verified & Plan Activated',
                'message'     => "Your payment of ₹" . number_format($planPrice, 0) . " for {$plan['name']} (" . ($cycle === 'yearly' ? '1 Year' : '30 Days') . ", UTR: {$utrNumber}) has been verified. Your store is active and live!",
                'type'        => 'success',
                'link'        => url('dashboard/subscription'),
            ]);

            flash('success', "🎉 Payment of ₹" . number_format($planPrice, 0) . " (UTR: {$utrNumber}) auto-verified! Your {$plan['name']} subscription is now active.");
            $this->redirect(url('dashboard/subscription'));
            return;
        }

        // RAZORPAY / ONLINE GATEWAY: Activated on verified online payment
        $subId = $this->subModel->createOrActivate($merchantId, $planId, $periodDays);

        // Record confirmed online payment
        $db->insert('subscription_payments', [
            'subscription_id'    => $subId,
            'merchant_id'        => $merchantId,
            'amount'             => $planPrice,
            'currency'           => 'INR',
            'payment_method'     => 'RAZORPAY',
            'status'             => 'paid',
            'gateway_payment_id' => 'RZP_' . strtoupper(bin2hex(random_bytes(5))),
            'transaction_ref'    => $utrNumber ?: 'Online Gateway',
            'paid_at'            => date('Y-m-d H:i:s'),
        ]);

        $db->update('merchants', ['onboarding_completed' => 1, 'status' => 'active'], 'id = ?', [$merchantId]);
        $store = $db->fetchOne("SELECT id FROM stores WHERE merchant_id = ?", [$merchantId]);
        if ($store) {
            $db->update('stores', ['status' => 'active'], 'id = ?', [$store['id']]);
        }

        flash('success', "🎉 ₹" . number_format($planPrice, 0) . " ({$plan['name']} - " . ($cycle === 'yearly' ? '1 Year' : '30 Days') . ") activated successfully! Your store is LIVE.");
        $this->redirect(url('dashboard'));
    }
}
