<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Order;

class OrderController extends Controller
{
    private Order $orderModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->orderModel = new Order($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $merchantId = current_merchant_id();
        $page = max(1, (int)$request->query('page', 1));
        $filters = [
            'search'         => sanitize_input($request->query('search')),
            'order_status'   => $request->query('order_status'),
            'payment_status' => $request->query('payment_status'),
        ];

        $ordersData = $this->orderModel->getAllForMerchant($merchantId, $filters, $page, 15);

        $this->view('merchant.orders.index', [
            'orders'  => $ordersData['data'],
            'total'   => $ordersData['total'],
            'page'    => $ordersData['page'],
            'pages'   => $ordersData['pages'],
            'filters' => $filters,
        ]);
    }

    public function show(Request $request, string $id): void
    {
        $merchantId = current_merchant_id();
        $order = $this->orderModel->findByIdAndMerchant((int)$id, $merchantId);

        if (!$order) {
            flash('error', 'Order not found.');
            $this->redirect(url('dashboard/orders'));
            return;
        }

        $this->view('merchant.orders.show', [
            'order' => $order,
        ]);
    }

    public function updateStatus(Request $request, string $id): void
    {
        $merchantId = current_merchant_id();
        $orderStatus = $request->input('order_status');
        $paymentStatus = $request->input('payment_status');

        $this->orderModel->updateStatus((int)$id, $merchantId, $orderStatus, $paymentStatus);

        flash('success', 'Order status updated successfully.');
        $this->redirect(url('dashboard/orders/' . $id));
    }

    public function invoice(Request $request, string $id): void
    {
        $merchantId = current_merchant_id();
        $order = $this->orderModel->findByIdAndMerchant((int)$id, $merchantId);

        if (!$order) {
            flash('error', 'Order not found.');
            $this->redirect(url('dashboard/orders'));
            return;
        }

        $this->view('merchant.orders.invoice', [
            'order' => $order,
        ]);
    }
}
