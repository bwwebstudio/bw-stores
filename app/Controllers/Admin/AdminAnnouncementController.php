<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Announcement;

class AdminAnnouncementController extends Controller
{
    private Announcement $announcementModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->announcementModel = new Announcement($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $announcements = $this->announcementModel->getAll();

        $this->view('admin.announcements.index', [
            'announcements' => $announcements,
        ]);
    }

    public function store(Request $request): void
    {
        $title = sanitize_input($request->input('title'));
        $message = sanitize_input($request->input('message'));

        if (empty($title) || empty($message)) {
            $this->backWithErrors(['title' => 'Title and message are required.']);
            return;
        }

        $this->announcementModel->create([
            'title'     => $title,
            'message'   => $message,
            'type'      => $request->input('type', 'info'),
            'is_active' => !empty($request->input('is_active')) ? 1 : 0,
        ]);

        flash('success', 'Announcement published to merchant dashboards!');
        $this->redirect(url('admin/announcements'));
    }

    public function delete(Request $request, string $id): void
    {
        $this->announcementModel->delete((int)$id);

        flash('success', 'Announcement removed.');
        $this->redirect(url('admin/announcements'));
    }
}
