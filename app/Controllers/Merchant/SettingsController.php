<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\User;
use App\Models\Merchant;

class SettingsController extends Controller
{
    private User $userModel;
    private Merchant $merchantModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->userModel = new User($this->app->getDatabase());
        $this->merchantModel = new Merchant($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $userId = current_user_id();
        $merchantId = current_merchant_id();

        $user = $this->userModel->findById($userId);
        $merchant = $this->merchantModel->findById($merchantId);

        $this->view('merchant.settings.index', [
            'user'     => $user,
            'merchant' => $merchant,
        ]);
    }

    public function updateProfile(Request $request): void
    {
        $userId = current_user_id();
        $merchantId = current_merchant_id();

        $name = sanitize_input($request->input('name'));
        $mobile = sanitize_input($request->input('mobile'));
        $businessName = sanitize_input($request->input('business_name'));
        $businessCategory = sanitize_input($request->input('business_category'));

        $this->userModel->update($userId, ['name' => $name, 'mobile' => $mobile]);
        $this->merchantModel->update($merchantId, [
            'business_name'     => $businessName,
            'business_category' => $businessCategory,
        ]);

        session()->set('user_name', $name);
        session()->set('merchant_business_name', $businessName);

        flash('success', 'Profile updated successfully.');
        $this->redirect(url('dashboard/settings'));
    }

    public function updatePassword(Request $request): void
    {
        $userId = current_user_id();
        $user = $this->userModel->findById($userId);

        $currentPassword = $request->input('current_password');
        $newPassword = $request->input('new_password');
        $confirmPassword = $request->input('confirm_password');

        if (!verify_password($currentPassword, $user['password_hash'])) {
            $this->backWithErrors(['current_password' => 'Incorrect current password.']);
            return;
        }

        if (strlen($newPassword) < 8) {
            $this->backWithErrors(['new_password' => 'New password must be at least 8 characters.']);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->backWithErrors(['confirm_password' => 'Passwords do not match.']);
            return;
        }

        $this->userModel->updatePassword($userId, $newPassword);

        flash('success', 'Password changed successfully!');
        $this->redirect(url('dashboard/settings'));
    }
}
