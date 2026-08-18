<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Subscription;

class AdminPaymentController extends Controller
{
    public function index(Request $request): void
    {
        $db = $this->app->getDatabase();
        $page = max(1, (int)$request->query('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $total = $db->fetchColumn("SELECT COUNT(*) FROM subscription_payments");

        $payments = $db->fetchAll("
            SELECT sp.*, 
                   m.business_name, 
                   u.name as user_name, 
                   u.email as user_email,
                   p.name as plan_name,
                   p.badge as plan_badge
            FROM subscription_payments sp
            JOIN merchants m ON m.id = sp.merchant_id
            JOIN users u ON u.id = m.user_id
            LEFT JOIN subscriptions s ON s.id = sp.subscription_id
            LEFT JOIN plans p ON p.id = s.plan_id
            ORDER BY sp.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");

        $this->view('admin.payments.index', [
            'payments' => $payments,
            'total'    => (int)$total,
            'page'     => $page,
            'pages'    => (int)ceil($total / $perPage),
        ]);
    }

    public function approve(Request $request, string $id): void
    {
        $paymentId = (int)$id;
        $db = $this->app->getDatabase();

        $payment = $db->fetchOne("SELECT * FROM subscription_payments WHERE id = ?", [$paymentId]);
        if (!$payment) {
            flash('error', 'Payment record not found.');
            $this->redirect(url('admin/payments'));
            return;
        }

        $merchantId = (int)$payment['merchant_id'];
        
        // Find which plan this subscription is for
        $sub = $db->fetchOne("SELECT * FROM subscriptions WHERE id = ?", [$payment['subscription_id']]);
        $planId = $sub ? (int)$sub['plan_id'] : 2;

        $plan = $db->fetchOne("SELECT * FROM plans WHERE id = ?", [$planId]);
        $planName = $plan ? $plan['name'] : 'BW Store Growth';

        $db->transaction(function ($db) use ($paymentId, $merchantId, $planId, $planName, $payment) {
            $db->update('subscription_payments', [
                'status'  => 'paid',
                'paid_at' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$paymentId]);

            $subModel = new Subscription($db);
            $subModel->createOrActivate($merchantId, $planId, 30);

            $db->update('merchants', [
                'status'               => 'active',
                'onboarding_completed' => 1,
            ], 'id = ?', [$merchantId]);

            $store = $db->fetchOne("SELECT id FROM stores WHERE merchant_id = ?", [$merchantId]);
            if ($store) {
                $db->update('stores', ['status' => 'active'], 'id = ?', [$store['id']]);
            }

            $db->insert('notifications', [
                'merchant_id' => $merchantId,
                'title'       => "🎉 Payment Verified & Approved for {$planName}!",
                'message'     => "Your payment (₹" . number_format($payment['amount'], 2) . ") for {$planName} has been verified and approved by admin. Your store is now LIVE for 30 days!",
                'type'        => 'success',
                'link'        => url('dashboard'),
            ]);
        });

        flash('success', "✅ UPI Payment #{$paymentId} verified and approved! Merchant #{$merchantId} store is now active for 30 days on {$planName}.");
        $this->redirect(url('admin/payments'));
    }

    public function reject(Request $request, string $id): void
    {
        $paymentId = (int)$id;
        $db = $this->app->getDatabase();

        $payment = $db->fetchOne("SELECT * FROM subscription_payments WHERE id = ?", [$paymentId]);
        if (!$payment) {
            flash('error', 'Payment record not found.');
            $this->redirect(url('admin/payments'));
            return;
        }

        $merchantId = (int)$payment['merchant_id'];
        $reason = sanitize_input($request->input('reason', 'Invalid UPI UTR Number / Payment not received in bank account'));

        $db->update('subscription_payments', ['status' => 'rejected'], 'id = ?', [$paymentId]);

        $db->insert('notifications', [
            'merchant_id' => $merchantId,
            'title'       => '⚠️ UPI Payment Verification Rejected',
            'message'     => "Your UPI payment verification was rejected by admin. Reason: {$reason}. Please submit a valid transaction UTR or retry checkout.",
            'type'        => 'danger',
            'link'        => url('dashboard/subscription/checkout'),
        ]);

        flash('warning', "Payment #{$paymentId} marked as rejected.");
        $this->redirect(url('admin/payments'));
    }
}
