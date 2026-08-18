<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\SupportTicket;

class SupportController extends Controller
{
    private SupportTicket $ticketModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->ticketModel = new SupportTicket($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $merchantId = current_merchant_id();
        $tickets = $this->ticketModel->getAllForMerchant($merchantId);

        $this->view('merchant.support.index', [
            'tickets' => $tickets,
        ]);
    }

    public function create(Request $request): void
    {
        $merchantId = current_merchant_id();
        $userId = current_user_id();

        $subject = sanitize_input($request->input('subject'));
        $message = sanitize_input($request->input('message'));

        if (empty($subject) || empty($message)) {
            $this->backWithErrors(['subject' => 'Subject and message are required.']);
            return;
        }

        $ticketId = $this->ticketModel->createTicket($merchantId, $userId, [
            'subject'  => $subject,
            'category' => sanitize_input($request->input('category', 'General')),
            'priority' => $request->input('priority', 'medium'),
            'message'  => $message,
        ]);

        flash('success', 'Support ticket opened successfully! Our team will respond shortly.');
        $this->redirect(url('dashboard/support/' . $ticketId));
    }

    public function show(Request $request, string $id): void
    {
        $merchantId = current_merchant_id();
        $ticket = $this->ticketModel->findByIdAndMerchant((int)$id, $merchantId);

        if (!$ticket) {
            flash('error', 'Ticket not found.');
            $this->redirect(url('dashboard/support'));
            return;
        }

        $this->view('merchant.support.show', [
            'ticket' => $ticket,
        ]);
    }

    public function reply(Request $request, string $id): void
    {
        $merchantId = current_merchant_id();
        $userId = current_user_id();
        $ticket = $this->ticketModel->findByIdAndMerchant((int)$id, $merchantId);

        if (!$ticket) {
            flash('error', 'Ticket not found.');
            $this->redirect(url('dashboard/support'));
            return;
        }

        $message = sanitize_input($request->input('message'));
        if (!empty($message)) {
            $this->ticketModel->addMessage((int)$id, $userId, 'merchant', $message);
            flash('success', 'Reply posted successfully.');
        }

        $this->redirect(url('dashboard/support/' . $id));
    }
}
