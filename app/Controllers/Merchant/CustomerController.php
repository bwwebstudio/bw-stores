<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    private Customer $customerModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->customerModel = new Customer($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $merchantId = current_merchant_id();
        $page = max(1, (int)$request->query('page', 1));
        $filters = ['search' => sanitize_input($request->query('search'))];

        $data = $this->customerModel->getAllForMerchant($merchantId, $filters, $page, 15);

        $this->view('merchant.customers.index', [
            'customers' => $data['data'],
            'total'     => $data['total'],
            'page'      => $data['page'],
            'pages'     => $data['pages'],
            'filters'   => $filters,
        ]);
    }

    public function show(Request $request, string $id): void
    {
        $merchantId = current_merchant_id();
        $customer = $this->customerModel->findByIdAndMerchant((int)$id, $merchantId);

        if (!$customer) {
            flash('error', 'Customer not found.');
            $this->redirect(url('dashboard/customers'));
            return;
        }

        $this->view('merchant.customers.show', [
            'customer' => $customer,
        ]);
    }
}
