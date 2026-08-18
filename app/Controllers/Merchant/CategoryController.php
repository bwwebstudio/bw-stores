<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    private Category $categoryModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->categoryModel = new Category($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $merchantId = current_merchant_id();
        $categories = $this->categoryModel->getAllForMerchant($merchantId);

        $this->view('merchant.categories.index', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): void
    {
        $merchantId = current_merchant_id();
        $name = sanitize_input($request->input('name'));

        if (empty($name)) {
            $this->backWithErrors(['name' => 'Category name is required.']);
            return;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $errors = validate_upload($file, ['jpg', 'jpeg', 'png', 'webp']);
            if (empty($errors)) {
                $filename = safe_filename($file['name']);
                $dest = BASE_PATH . '/public/uploads/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $imagePath = 'public/uploads/' . $filename;
                }
            }
        }

        $this->categoryModel->create($merchantId, [
            'name'        => $name,
            'slug'        => slugify($request->input('slug') ?: $name),
            'description' => sanitize_input($request->input('description')),
            'image'       => $imagePath,
            'sort_order'  => (int)$request->input('sort_order', 0),
            'status'      => $request->input('status', 'active'),
        ]);

        flash('success', 'Category created successfully!');
        $this->redirect(url('dashboard/categories'));
    }

    public function update(Request $request, string $id): void
    {
        $merchantId = current_merchant_id();
        $category = $this->categoryModel->findByIdAndMerchant((int)$id, $merchantId);

        if (!$category) {
            flash('error', 'Category not found.');
            $this->redirect(url('dashboard/categories'));
            return;
        }

        $name = sanitize_input($request->input('name'));
        if (empty($name)) {
            $this->backWithErrors(['name' => 'Category name is required.']);
            return;
        }

        $imagePath = $category['image'];
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $errors = validate_upload($file, ['jpg', 'jpeg', 'png', 'webp']);
            if (empty($errors)) {
                $filename = safe_filename($file['name']);
                $dest = BASE_PATH . '/public/uploads/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $imagePath = 'public/uploads/' . $filename;
                }
            }
        }

        $this->categoryModel->update((int)$id, $merchantId, [
            'name'        => $name,
            'slug'        => slugify($request->input('slug') ?: $name),
            'description' => sanitize_input($request->input('description')),
            'image'       => $imagePath,
            'sort_order'  => (int)$request->input('sort_order', 0),
            'status'      => $request->input('status', 'active'),
        ]);

        flash('success', 'Category updated successfully!');
        $this->redirect(url('dashboard/categories'));
    }

    public function delete(Request $request, string $id): void
    {
        $merchantId = current_merchant_id();
        $this->categoryModel->delete((int)$id, $merchantId);

        flash('success', 'Category deleted successfully.');
        $this->redirect(url('dashboard/categories'));
    }
}
