<?php

use App\Core\View;
View::layout('layouts.admin');

View::section('title'); ?>Announcements<?php View::endSection();
View::section('page_title'); ?>Merchant Dashboard Announcements<?php View::endSection();

View::section('content'); ?>

<div class="row g-4">
    <!-- Left 4 Columns: Create Announcement -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="fw-700 mb-3"><i class="bi bi-megaphone text-primary me-2"></i>New Announcement</h5>

                <form method="POST" action="<?= url('admin/announcements/create') ?>" data-loading>
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Announcement Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Scheduled System Upgrade Tonight" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Banner Style / Type</label>
                        <select name="type" class="form-select">
                            <option value="info">Info (Blue)</option>
                            <option value="success">Success (Green)</option>
                            <option value="warning">Warning (Yellow)</option>
                            <option value="danger">Urgent / Alert (Red)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message Content <span class="text-danger">*</span></label>
                        <textarea name="message" rows="4" class="form-control" placeholder="Message displayed to all logged-in merchants..." required></textarea>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" checked>
                        <label class="form-check-label fw-500" for="is_active">Publish Immediately</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-700">
                        <i class="bi bi-broadcast me-1"></i> Broadcast Announcement
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right 8 Columns: Announcement List -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0">
                <div class="p-3 border-bottom">
                    <h5 class="fw-700 mb-0">Broadcast History (<?= count($announcements) ?>)</h5>
                </div>

                <?php if (empty($announcements)): ?>
                    <div class="p-5 text-center text-muted">No active announcements.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($announcements as $a): ?>
                        <div class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge bg-<?= $a['type'] ?>-subtle text-<?= $a['type'] ?> border me-2"><?= strtoupper($a['type']) ?></span>
                                    <strong class="text-dark"><?= e($a['title']) ?></strong>
                                    <div class="text-muted small mt-1"><?= nl2br(e($a['message'])) ?></div>
                                    <div class="text-muted text-xs mt-2"><?= date('M d, Y H:i', strtotime($a['created_at'])) ?></div>
                                </div>
                                <form method="POST" action="<?= url('admin/announcements/' . $a['id'] . '/delete') ?>" onsubmit="return confirm('Remove announcement?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php View::endSection(); ?>
