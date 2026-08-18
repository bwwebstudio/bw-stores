<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Store;
use App\Models\StoreSetting;

class PaymentController extends Controller
{
    private Store $storeModel;
    private StoreSetting $settingModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->storeModel = new Store($this->app->getDatabase());
        $this->settingModel = new StoreSetting($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $merchantId = current_merchant_id();
        $store = $this->storeModel->findByMerchantId($merchantId);
        $settings = $store ? $this->settingModel->findByStoreId($store['id']) : null;

        $this->view('merchant.payments.index', [
            'store'    => $store,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): void
    {
        $merchantId = current_merchant_id();
        $store = $this->storeModel->findByMerchantId($merchantId);

        if (!$store) {
            flash('error', 'Store not found.');
            $this->redirect(url('dashboard'));
            return;
        }

        $keyId = sanitize_input($request->input('razorpay_key_id', ''));
        $keySecret = sanitize_input($request->input('razorpay_key_secret', ''));
        $isConnected = (!empty($keyId) && !empty($keySecret)) ? 1 : 0;
        $codEnabled = $request->input('cod_enabled') ? 1 : 0;

        $merchantUpi = sanitize_input($request->input('merchant_upi_id', ''));
        $merchantUpiName = sanitize_input($request->input('merchant_upi_name', ''));
        $upiEnabled = $request->input('upi_enabled') ? 1 : 0;

        $this->settingModel->createOrUpdate($store['id'], $merchantId, [
            'razorpay_key_id'     => $keyId,
            'razorpay_key_secret' => $keySecret,
            'razorpay_connected'  => $isConnected,
            'merchant_upi_id'     => $merchantUpi,
            'merchant_upi_name'   => $merchantUpiName,
            'upi_enabled'         => $upiEnabled,
            'cod_enabled'         => $codEnabled,
        ]);

        flash('success', '✅ Store payment gateways (UPI, Razorpay, COD) updated successfully!');
        $this->redirect(url('dashboard/payments'));
    }
}
