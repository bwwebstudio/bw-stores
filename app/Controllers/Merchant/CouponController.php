<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    private Coupon $couponModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->couponModel = new Coupon($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $merchantId = current_merchant_id();
        $coupons = $this->couponModel->getAllForMerchant($merchantId);

        $this->view('merchant.coupons.index', [
            'coupons' => $coupons,
        ]);
    }

    public function store(Request $request): void
    {
        $merchantId = current_merchant_id();
        $code = strtoupper(trim($request->input('code')));

        if (empty($code)) {
            $this->backWithErrors(['code' => 'Coupon code is required.']);
            return;
        }

        $existing = $this->couponModel->findByCodeAndMerchant($code, $merchantId);
        if ($existing) {
            $this->backWithErrors(['code' => 'A coupon with this code already exists.']);
            return;
        }

        $this->couponModel->create($merchantId, [
            'code'         => $code,
            'type'         => $request->input('type', 'percentage'),
            'value'        => (float)$request->input('value', 0),
            'min_order'    => (float)$request->input('min_order', 0),
            'max_discount' => $request->input('max_discount') ? (float)$request->input('max_discount') : null,
            'usage_limit'  => $request->input('usage_limit') ? (int)$request->input('usage_limit') : null,
            'expires_at'   => $request->input('expires_at') ?: null,
            'is_active'    => !empty($request->input('is_active')) ? 1 : 0,
        ]);

        flash('success', 'Coupon created successfully!');
        $this->redirect(url('dashboard/coupons'));
    }

    public function update(Request $request, string $id): void
    {
        $merchantId = current_merchant_id();

        $this->couponModel->update((int)$id, $merchantId, [
            'code'         => strtoupper(trim($request->input('code'))),
            'type'         => $request->input('type', 'percentage'),
            'value'        => (float)$request->input('value', 0),
            'min_order'    => (float)$request->input('min_order', 0),
            'max_discount' => $request->input('max_discount') ? (float)$request->input('max_discount') : null,
            'usage_limit'  => $request->input('usage_limit') ? (int)$request->input('usage_limit') : null,
            'expires_at'   => $request->input('expires_at') ?: null,
            'is_active'    => !empty($request->input('is_active')) ? 1 : 0,
        ]);

        flash('success', 'Coupon updated successfully!');
        $this->redirect(url('dashboard/coupons'));
    }

    public function delete(Request $request, string $id): void
    {
        $merchantId = current_merchant_id();
        $this->couponModel->delete((int)$id, $merchantId);

        flash('success', 'Coupon deleted successfully.');
        $this->redirect(url('dashboard/coupons'));
    }
}
