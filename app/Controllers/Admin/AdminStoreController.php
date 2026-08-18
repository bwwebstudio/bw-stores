<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Store;

class AdminStoreController extends Controller
{
    private Store $storeModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->storeModel = new Store($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $page = max(1, (int)$request->query('page', 1));
        $search = sanitize_input($request->query('search'));

        $data = $this->storeModel->getAll($page, 20, $search);

        $this->view('admin.stores.index', [
            'stores' => $data['data'],
            'total'  => $data['total'],
            'page'   => $data['page'],
            'pages'  => $data['pages'],
            'search' => $search,
        ]);
    }
}
