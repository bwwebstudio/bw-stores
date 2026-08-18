<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\SupportTicket;

class AdminSupportController extends Controller
{
    private SupportTicket $ticketModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->ticketModel = new SupportTicket($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $page = max(1, (int)$request->query('page', 1));
        $status = $request->query('status');

        $data = $this->ticketModel->getAllForAdmin(['status' => $status], $page, 20);

        $this->view('admin.support.index', [
            'tickets' => $data['data'],
            'total'   => $data['total'],
            'page'    => $data['page'],
            'pages'   => $data['pages'],
            'status'  => $status,
        ]);
    }

    public function show(Request $request, string $id): void
    {
        $ticket = $this->ticketModel->findByIdForAdmin((int)$id);
        if (!$ticket) {
            flash('error', 'Ticket not found.');
            $this->redirect(url('admin/support'));
            return;
        }

        $this->view('admin.support.show', [
            'ticket' => $ticket,
        ]);
    }

    public function reply(Request $request, string $id): void
    {
        $userId = current_user_id();
        $message = sanitize_input($request->input('message'));

        if (!empty($message)) {
            $this->ticketModel->addMessage((int)$id, $userId, 'admin', $message);
        }

        if ($status = $request->input('status')) {
            $this->ticketModel->updateStatus((int)$id, $status);
        }

        flash('success', 'Admin response sent to merchant.');
        $this->redirect(url('admin/support/' . $id));
    }
}
