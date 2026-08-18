<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Store;
use App\Models\StoreSetting;

class StoreDesignController extends Controller
{
    private Store $storeModel;
    private StoreSetting $settingModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->storeModel = new Store($this->app->getDatabase());
        $this->settingModel = new StoreSetting($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $merchantId = current_merchant_id();
        $store = $this->storeModel->findByMerchantId($merchantId);
        $settings = $store ? $this->settingModel->findByStoreId($store['id']) : null;

        $this->view('merchant.store_design.index', [
            'store'    => $store,
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): void
    {
        $merchantId = current_merchant_id();
        $store = $this->storeModel->findByMerchantId($merchantId);

        if (!$store) {
            flash('error', 'Store not found.');
            $this->redirect(url('dashboard'));
            return;
        }

        $logoPath = $store['logo'];
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $errors = validate_upload($file, ['jpg', 'jpeg', 'png', 'webp', 'svg']);
            if (empty($errors)) {
                $filename = safe_filename($file['name']);
                $dest = BASE_PATH . '/public/uploads/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $logoPath = 'public/uploads/' . $filename;
                }
            }
        }

        $faviconPath = $store['favicon'];
        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            $errors = validate_upload($file, ['ico', 'png', 'svg', 'webp']);
            if (empty($errors)) {
                $filename = safe_filename($file['name']);
                $dest = BASE_PATH . '/public/uploads/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $faviconPath = 'public/uploads/' . $filename;
                }
            }
        }

        $heroImagePath = $request->input('existing_hero_image');
        if ($request->hasFile('hero_image')) {
            $file = $request->file('hero_image');
            $errors = validate_upload($file, ['jpg', 'jpeg', 'png', 'webp']);
            if (empty($errors)) {
                $filename = safe_filename($file['name']);
                $dest = BASE_PATH . '/public/uploads/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $heroImagePath = 'public/uploads/' . $filename;
                }
            }
        }

        // Update Store Base
        $storeName = sanitize_input($request->input('store_name', $store['name']));
        $this->storeModel->update($store['id'], [
            'name'        => $storeName,
            'logo'        => $logoPath,
            'favicon'     => $faviconPath,
            'description' => sanitize_input($request->input('description')),
        ]);

        $theme = in_array($request->input('theme_name'), ['modern', 'fashion', 'business']) ? $request->input('theme_name') : 'modern';

        // Check theme limit for Starter plan
        $db = $this->app->getDatabase();
        $sub = $db->fetchOne("
            SELECT p.max_themes, p.name as plan_name
            FROM subscriptions s
            JOIN plans p ON p.id = s.plan_id
            WHERE s.merchant_id = ? AND s.status = 'active'
            ORDER BY s.id DESC LIMIT 1
        ", [$merchantId]);

        if ($sub && (int)$sub['max_themes'] === 1 && $theme !== 'modern') {
            flash('warning', "The '{$theme}' theme requires the Growth or Enterprise plan. Your {$sub['plan_name']} plan includes the Modern theme. Upgrade anytime in Subscription.");
            $theme = 'modern';
        }

        // Update Store Settings
        $this->settingModel->createOrUpdate($store['id'], $merchantId, [
            'logo'             => $logoPath,
            'favicon'          => $faviconPath,
            'primary_color'    => sanitize_input($request->input('primary_color', '#2563EB')),
            'secondary_color'  => sanitize_input($request->input('secondary_color', '#0F172A')),
            'hero_title'       => sanitize_input($request->input('hero_title')),
            'hero_subtitle'    => sanitize_input($request->input('hero_subtitle')),
            'hero_image'       => $heroImagePath,
            'whatsapp_number'  => sanitize_input($request->input('whatsapp_number')),
            'contact_email'    => sanitize_email($request->input('contact_email')),
            'contact_phone'    => sanitize_input($request->input('contact_phone')),
            'business_address' => sanitize_input($request->input('business_address')),
            'facebook_url'     => sanitize_input($request->input('facebook_url')),
            'instagram_url'    => sanitize_input($request->input('instagram_url')),
            'twitter_url'      => sanitize_input($request->input('twitter_url')),
            'footer_text'      => sanitize_input($request->input('footer_text')),
            'theme_name'       => $theme,
        ]);

        session()->set('store_name', $storeName);

        flash('success', 'Store appearance and branding updated successfully!');
        $this->redirect(url('dashboard/store-design'));
    }
}
