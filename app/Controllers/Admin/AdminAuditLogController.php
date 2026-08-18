<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Services\AuditService;

class AdminAuditLogController extends Controller
{
    public function index(Request $request): void
    {
        $auditService = new AuditService();
        $page = max(1, (int)$request->query('page', 1));
        $action = $request->query('action');

        $data = $auditService->getAll($page, 30, null, $action);

        $this->view('admin.audit_logs.index', [
            'logs'   => $data['data'],
            'total'  => $data['total'],
            'page'   => $data['page'],
            'pages'  => $data['pages'],
            'action' => $action,
        ]);
    }
}
