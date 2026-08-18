<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Subscription;

class AdminSubscriptionController extends Controller
{
    private Subscription $subModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->subModel = new Subscription($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $page = max(1, (int)$request->query('page', 1));
        $status = $request->query('status');

        $data = $this->subModel->getAllForAdmin(['status' => $status], $page, 20);

        $this->view('admin.subscriptions.index', [
            'subscriptions' => $data['data'],
            'total'         => $data['total'],
            'page'          => $data['page'],
            'pages'         => $data['pages'],
            'status'        => $status,
        ]);
    }
}
