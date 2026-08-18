<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Merchant;
use App\Models\Store;
use App\Models\Subscription;

class AdminMerchantController extends Controller
{
    private Merchant $merchantModel;
    private Store $storeModel;
    private Subscription $subModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $db = $this->app->getDatabase();
        $this->merchantModel = new Merchant($db);
        $this->storeModel = new Store($db);
        $this->subModel = new Subscription($db);
    }

    public function index(Request $request): void
    {
        $page = max(1, (int)$request->query('page', 1));
        $status = $request->query('status');

        $data = $this->merchantModel->getAllPaginated($page, 20, $status);

        $this->view('admin.merchants.index', [
            'merchants' => $data['data'],
            'total'     => $data['total'],
            'page'      => $data['page'],
            'pages'     => $data['pages'],
            'status'    => $status,
        ]);
    }

    public function show(Request $request, string $id): void
    {
        $merchant = $this->merchantModel->findWithUser((int)$id);
        if (!$merchant) {
            flash('error', 'Merchant not found.');
            $this->redirect(url('admin/merchants'));
            return;
        }

        $store = $this->storeModel->findByMerchantId((int)$id);
        $subscription = $this->subModel->findByMerchantId((int)$id);

        $this->view('admin.merchants.show', [
            'merchant'     => $merchant,
            'store'        => $store,
            'subscription' => $subscription,
        ]);
    }

    public function suspend(Request $request, string $id): void
    {
        $reason = sanitize_input($request->input('reason', 'Policy violation / administrative suspension'));
        $this->merchantModel->suspend((int)$id, $reason);

        // Also suspend associated store
        $store = $this->storeModel->findByMerchantId((int)$id);
        if ($store) {
            $this->storeModel->update($store['id'], ['status' => 'suspended']);
        }

        flash('warning', "Merchant store has been suspended. Reason: {$reason}");
        $this->redirect(url('admin/merchants/' . $id));
    }

    public function activate(Request $request, string $id): void
    {
        $this->merchantModel->activate((int)$id);

        $store = $this->storeModel->findByMerchantId((int)$id);
        if ($store) {
            $this->storeModel->update($store['id'], ['status' => 'active']);
        }

        flash('success', 'Merchant account and store reactivated successfully.');
        $this->redirect(url('admin/merchants/' . $id));
    }

    public function extendSubscription(Request $request, string $id): void
    {
        $days = (int)$request->input('days', 30);
        $this->subModel->createOrActivate((int)$id, 1, $days);

        flash('success', "Subscription extended by {$days} days for this merchant.");
        $this->redirect(url('admin/merchants/' . $id));
    }

    public function toggleStore(Request $request, string $id): void
    {
        $merchantId = (int)$id;
        $store = $this->storeModel->findByMerchantId($merchantId);
        if (!$store) {
            flash('error', 'Store not found for this merchant.');
            $this->redirect(url('admin/merchants/' . $id));
            return;
        }

        $newStatus = $store['status'] === 'active' ? 'suspended' : 'active';
        $this->storeModel->update($store['id'], ['status' => $newStatus]);
        if ($newStatus === 'suspended') {
            $this->merchantModel->suspend($merchantId, 'Store disabled by admin');
            flash('warning', "Store #{$store['id']} ({$store['name']}) has been DISABLED.");
        } else {
            $this->merchantModel->activate($merchantId);
            flash('success', "Store #{$store['id']} ({$store['name']}) has been ENABLED & Activated.");
        }

        $this->redirect(url('admin/merchants/' . $id));
    }

    public function delete(Request $request, string $id): void
    {
        $merchantId = (int)$id;
        $db = $this->app->getDatabase();

        $merchant = $this->merchantModel->findById($merchantId);
        if (!$merchant) {
            flash('error', 'Merchant not found.');
            $this->redirect(url('admin/merchants'));
            return;
        }

        $userId = (int)$merchant['user_id'];

        $db->transaction(function ($db) use ($merchantId, $userId) {
            $db->query("DELETE FROM audit_logs WHERE user_id = ?", [$userId]);
            $db->query("DELETE FROM support_messages WHERE user_id = ? OR ticket_id IN (SELECT id FROM support_tickets WHERE merchant_id = ?)", [$userId, $merchantId]);
            $db->query("DELETE FROM support_tickets WHERE merchant_id = ?", [$merchantId]);
            $db->query("DELETE FROM notifications WHERE merchant_id = ?", [$merchantId]);
            $db->query("DELETE FROM coupon_usages WHERE merchant_id = ? OR coupon_id IN (SELECT id FROM coupons WHERE merchant_id = ?)", [$merchantId, $merchantId]);
            $db->query("DELETE FROM coupons WHERE merchant_id = ?", [$merchantId]);
            $db->query("DELETE FROM inventory_transactions WHERE merchant_id = ?", [$merchantId]);
            $db->query("DELETE FROM product_variants WHERE merchant_id = ?", [$merchantId]);
            $db->query("DELETE FROM products WHERE merchant_id = ?", [$merchantId]);
            $db->query("DELETE FROM categories WHERE merchant_id = ?", [$merchantId]);
            $db->query("DELETE FROM order_items WHERE merchant_id = ? OR order_id IN (SELECT id FROM orders WHERE merchant_id = ?)", [$merchantId, $merchantId]);
            $db->query("DELETE FROM payments WHERE order_id IN (SELECT id FROM orders WHERE merchant_id = ?)", [$merchantId]);
            $db->query("DELETE FROM orders WHERE merchant_id = ?", [$merchantId]);
            $db->query("DELETE FROM customers WHERE merchant_id = ?", [$merchantId]);
            $db->query("DELETE FROM store_settings WHERE merchant_id = ?", [$merchantId]);
            $db->query("DELETE FROM stores WHERE merchant_id = ?", [$merchantId]);
            $db->query("DELETE FROM subscription_payments WHERE merchant_id = ?", [$merchantId]);
            $db->query("DELETE FROM subscriptions WHERE merchant_id = ?", [$merchantId]);
            $db->query("DELETE FROM merchants WHERE id = ?", [$merchantId]);
            $db->query("DELETE FROM users WHERE id = ?", [$userId]);
        });

        flash('success', "Merchant #{$merchantId} and all associated store data deleted permanently.");
        $this->redirect(url('admin/merchants'));
    }

    public function resetPassword(Request $request, string $id): void
    {
        $merchantId = (int)$id;
        $merchant = $this->merchantModel->findWithUser($merchantId);
        if (!$merchant) {
            flash('error', 'Merchant not found.');
            $this->redirect(url('admin/merchants'));
            return;
        }

        $newPassword = $request->input('new_password', '');
        if (strlen($newPassword) < 6) {
            flash('error', 'New password must be at least 6 characters long.');
            $this->redirect(url('admin/merchants/' . $id));
            return;
        }

        $userId = (int)$merchant['user_id'];
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->app->getDatabase()->update('users', ['password_hash' => $hash], 'id = ?', [$userId]);

        // Audit Log
        $this->app->getDatabase()->insert('audit_logs', [
            'user_id'    => current_user_id() ?: $userId,
            'action'     => 'admin_reset_merchant_password',
            'details'    => "Admin reset password for Merchant #{$merchantId} ({$merchant['email']})",
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Admin Panel',
        ]);

        flash('success', "🔑 Password for Merchant #{$merchantId} ({$merchant['email']}) has been successfully reset to: '{$newPassword}'. Please securely share this with the merchant.");
        $this->redirect(url('admin/merchants/' . $id));
    }
}
