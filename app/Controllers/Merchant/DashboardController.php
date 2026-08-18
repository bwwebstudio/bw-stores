<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;

/**
 * Merchant Dashboard Controller
 */
class DashboardController extends Controller
{
    private \App\Models\Order $orderModel;
    private \App\Models\Product $productModel;
    private \App\Models\Subscription $subModel;

    public function __construct(\App\Core\Application $app)
    {
        parent::__construct($app);
        $this->orderModel = new \App\Models\Order();
        $this->productModel = new \App\Models\Product();
        $this->subModel = new \App\Models\Subscription();
    }

    /**
     * Show merchant dashboard.
     * GET /dashboard
     */
    public function index(Request $request): void
    {
        $user = current_user();
        $merchant = current_merchant();
        $store = current_store();
        $merchantId = (int)$merchant['id'];

        $todaySales = $this->orderModel->getTodaySales($merchantId);
        $totalOrders = $this->orderModel->countByMerchant($merchantId);
        $totalProducts = $this->productModel->countByMerchant($merchantId);
        $recentOrders = $this->orderModel->findByMerchant($merchantId, [], 5, 0);
        $subscription = $this->subModel->findByMerchantId($merchantId);

        // Calculate subscription days remaining
        $daysRemaining = 30;
        $expiryDate = null;
        if ($subscription && !empty($subscription['current_period_end'])) {
            $end = strtotime($subscription['current_period_end']);
            $now = time();
            $daysRemaining = max(0, ceil(($end - $now) / 86400));
            $expiryDate = date('d M Y', $end);
        }

        $this->view('merchant.dashboard', [
            'user'          => $user,
            'merchant'      => $merchant,
            'store'         => $store,
            'todaySales'    => $todaySales,
            'totalOrders'   => $totalOrders,
            'totalProducts' => $totalProducts,
            'recentOrders'  => $recentOrders,
            'subscription'  => $subscription,
            'daysRemaining' => $daysRemaining,
            'expiryDate'    => $expiryDate,
        ]);
    }
}
