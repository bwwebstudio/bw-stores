<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Core\Request;

class AdminSettingController extends Controller
{
    public function index(Request $request): void
    {
        $db = $this->app->getDatabase();
        $settings = $db->fetchAll("SELECT * FROM admin_settings");
        $settingsMap = [];
        foreach ($settings as $s) {
            $settingsMap[$s['setting_key']] = $s['setting_value'];
        }

        $this->view('admin.settings.index', [
            'settings' => $settingsMap,
        ]);
    }

    public function update(Request $request): void
    {
        $db = $this->app->getDatabase();
        $inputs = $request->all();

        foreach ($inputs as $key => $val) {
            if ($key === '_csrf_token' || $key === '_method') continue;

            $existing = $db->fetchOne("SELECT id FROM admin_settings WHERE setting_key = ?", [$key]);
            if ($existing) {
                $db->update('admin_settings', ['setting_value' => sanitize_input($val)], 'setting_key = ?', [$key]);
            } else {
                $db->insert('admin_settings', [
                    'setting_key'   => $key,
                    'setting_value' => sanitize_input($val),
                ]);
            }
        }

        if (isset($inputs['subscription_price'])) {
            $newPrice = (float)$inputs['subscription_price'];
            $db->update('plans', ['price' => $newPrice], "slug = 'bw-store'");
        }

        flash('success', 'Global platform settings and plan pricing updated everywhere successfully.');
        $this->redirect(url('admin/settings'));
    }
}
