<?php

namespace App\Controllers\Merchant;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->productModel = new Product($this->app->getDatabase());
        $this->categoryModel = new Category($this->app->getDatabase());
    }

    public function index(Request $request): void
    {
        $merchantId = current_merchant_id();
        $page = max(1, (int)$request->query('page', 1));
        $filters = [
            'search'      => sanitize_input($request->query('search')),
            'category_id' => $request->query('category_id'),
            'status'      => $request->query('status'),
        ];

        $productsData = $this->productModel->getAllForMerchant($merchantId, $filters, $page, 12);
        $categories = $this->categoryModel->getAllForMerchant($merchantId);

        $this->view('merchant.products.index', [
            'products'   => $productsData['data'],
            'total'      => $productsData['total'],
            'page'       => $productsData['page'],
            'pages'      => $productsData['pages'],
            'filters'    => $filters,
            'categories' => $categories,
        ]);
    }

    public function create(Request $request): void
    {
        $merchantId = current_merchant_id();
        $categories = $this->categoryModel->getAllForMerchant($merchantId);

        $this->view('merchant.products.create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): void
    {
        $merchantId = current_merchant_id();
        $db = $this->app->getDatabase();

        // Enforce Plan Product Limit (e.g. Starter 10 Products)
        $sub = $db->fetchOne("
            SELECT p.max_products, p.name as plan_name
            FROM subscriptions s
            JOIN plans p ON p.id = s.plan_id
            WHERE s.merchant_id = ? AND s.status = 'active'
            ORDER BY s.id DESC LIMIT 1
        ", [$merchantId]);

        if ($sub && (int)$sub['max_products'] > 0) {
            $currentCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM products WHERE merchant_id = ?", [$merchantId]);
            if ($currentCount >= (int)$sub['max_products']) {
                flash('error', "⚠️ Your {$sub['plan_name']} plan has reached its limit of {$sub['max_products']} products. Please upgrade to the Growth plan for unlimited products.");
                $this->redirect(url('dashboard/subscription'));
                return;
            }
        }

        $name = sanitize_input($request->input('name'));
        if (empty($name)) {
            $this->backWithErrors(['name' => 'Product name is required.'], $request->all());
            return;
        }

        $price = (float)$request->input('price', 0);
        if ($price < 0) {
            $this->backWithErrors(['price' => 'Price cannot be negative.'], $request->all());
            return;
        }

        // Handle uploaded images
        $uploadedImages = [];
        if ($request->hasFile('images')) {
            $files = $_FILES['images'];
            $fileCount = is_array($files['name']) ? count($files['name']) : 1;

            for ($i = 0; $i < $fileCount; $i++) {
                $fileItem = [
                    'name'     => is_array($files['name']) ? $files['name'][$i] : $files['name'],
                    'type'     => is_array($files['type']) ? $files['type'][$i] : $files['type'],
                    'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
                    'error'    => is_array($files['error']) ? $files['error'][$i] : $files['error'],
                    'size'     => is_array($files['size']) ? $files['size'][$i] : $files['size'],
                ];

                if ($fileItem['error'] === UPLOAD_ERR_OK) {
                    $errors = validate_upload($fileItem, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                    if (empty($errors)) {
                        $filename = safe_filename($fileItem['name']);
                        $dest = BASE_PATH . '/public/uploads/' . $filename;
                        if (move_uploaded_file($fileItem['tmp_name'], $dest)) {
                            $uploadedImages[] = 'public/uploads/' . $filename;
                        }
                    }
                }
            }
        }

        // Parse Variants from dynamic input
        $variants = [];
        $variantNames = $request->input('variant_name', []);
        $variantSkus = $request->input('variant_sku', []);
        $variantPrices = $request->input('variant_price', []);
        $variantStocks = $request->input('variant_stock', []);

        if (is_array($variantNames)) {
            foreach ($variantNames as $k => $vName) {
                if (!empty($vName)) {
                    $variants[] = [
                        'name'  => sanitize_input($vName),
                        'sku'   => sanitize_input($variantSkus[$k] ?? ''),
                        'price' => (float)($variantPrices[$k] ?? $price),
                        'stock' => (int)($variantStocks[$k] ?? 0),
                    ];
                }
            }
        }

        $productId = $this->productModel->create($merchantId, [
            'name'              => $name,
            'slug'              => slugify($request->input('slug') ?: $name),
            'sku'               => sanitize_input($request->input('sku')),
            'category_id'       => $request->input('category_id'),
            'price'             => $price,
            'compare_price'     => $request->input('compare_price') ? (float)$request->input('compare_price') : null,
            'stock'             => (int)$request->input('stock', 0),
            'low_stock_limit'   => (int)$request->input('low_stock_limit', 5),
            'brand'             => sanitize_input($request->input('brand')),
            'weight'            => $request->input('weight') ? (float)$request->input('weight') : null,
            'description'       => clean_html($request->input('description')),
            'short_description' => sanitize_input($request->input('short_description')),
            'status'            => $request->input('status', 'published'),
            'is_featured'       => !empty($request->input('is_featured')) ? 1 : 0,
            'images'            => $uploadedImages,
            'seo_title'         => sanitize_input($request->input('seo_title')),
            'seo_description'   => sanitize_input($request->input('seo_description')),
            'variants'          => $variants,
        ]);

        flash('success', 'Product created successfully!');
        $this->redirect(url('dashboard/products'));
    }

    public function edit(Request $request, string $id): void
    {
        $merchantId = current_merchant_id();
        $product = $this->productModel->findByIdAndMerchant((int)$id, $merchantId);

        if (!$product) {
            flash('error', 'Product not found.');
            $this->redirect(url('dashboard/products'));
            return;
        }

        $categories = $this->categoryModel->getAllForMerchant($merchantId);

        $this->view('merchant.products.edit', [
            'product'    => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, string $id): void
    {
        $merchantId = current_merchant_id();
        $product = $this->productModel->findByIdAndMerchant((int)$id, $merchantId);

        if (!$product) {
            flash('error', 'Product not found.');
            $this->redirect(url('dashboard/products'));
            return;
        }

        $name = sanitize_input($request->input('name'));
        if (empty($name)) {
            $this->backWithErrors(['name' => 'Product name is required.']);
            return;
        }

        $price = (float)$request->input('price', 0);
        $images = $product['images'] ?? [];

        // Upload any additional images
        if ($request->hasFile('images')) {
            $files = $_FILES['images'];
            $fileCount = is_array($files['name']) ? count($files['name']) : 1;

            for ($i = 0; $i < $fileCount; $i++) {
                $fileItem = [
                    'name'     => is_array($files['name']) ? $files['name'][$i] : $files['name'],
                    'type'     => is_array($files['type']) ? $files['type'][$i] : $files['type'],
                    'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
                    'error'    => is_array($files['error']) ? $files['error'][$i] : $files['error'],
                    'size'     => is_array($files['size']) ? $files['size'][$i] : $files['size'],
                ];

                if ($fileItem['error'] === UPLOAD_ERR_OK) {
                    $errors = validate_upload($fileItem, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                    if (empty($errors)) {
                        $filename = safe_filename($fileItem['name']);
                        $dest = BASE_PATH . '/public/uploads/' . $filename;
                        if (move_uploaded_file($fileItem['tmp_name'], $dest)) {
                            $images[] = 'public/uploads/' . $filename;
                        }
                    }
                }
            }
        }

        // Handle image removals if checked
        $removeImages = $request->input('remove_images', []);
        if (is_array($removeImages) && !empty($removeImages)) {
            $images = array_values(array_filter($images, fn($img) => !in_array($img, $removeImages)));
        }

        // Parse Variants
        $variants = [];
        $variantNames = $request->input('variant_name', []);
        $variantSkus = $request->input('variant_sku', []);
        $variantPrices = $request->input('variant_price', []);
        $variantStocks = $request->input('variant_stock', []);

        if (is_array($variantNames)) {
            foreach ($variantNames as $k => $vName) {
                if (!empty($vName)) {
                    $variants[] = [
                        'name'  => sanitize_input($vName),
                        'sku'   => sanitize_input($variantSkus[$k] ?? ''),
                        'price' => (float)($variantPrices[$k] ?? $price),
                        'stock' => (int)($variantStocks[$k] ?? 0),
                    ];
                }
            }
        }

        $this->productModel->update((int)$id, $merchantId, [
            'name'              => $name,
            'slug'              => slugify($request->input('slug') ?: $name),
            'sku'               => sanitize_input($request->input('sku')),
            'category_id'       => $request->input('category_id'),
            'price'             => $price,
            'compare_price'     => $request->input('compare_price') ? (float)$request->input('compare_price') : null,
            'stock'             => (int)$request->input('stock', 0),
            'low_stock_limit'   => (int)$request->input('low_stock_limit', 5),
            'brand'             => sanitize_input($request->input('brand')),
            'weight'            => $request->input('weight') ? (float)$request->input('weight') : null,
            'description'       => clean_html($request->input('description')),
            'short_description' => sanitize_input($request->input('short_description')),
            'status'            => $request->input('status', 'published'),
            'is_featured'       => !empty($request->input('is_featured')) ? 1 : 0,
            'images'            => $images,
            'seo_title'         => sanitize_input($request->input('seo_title')),
            'seo_description'   => sanitize_input($request->input('seo_description')),
            'variants'          => $variants,
        ]);

        flash('success', 'Product updated successfully!');
        $this->redirect(url('dashboard/products'));
    }

    public function delete(Request $request, string $id): void
    {
        $merchantId = current_merchant_id();
        $this->productModel->delete((int)$id, $merchantId);

        flash('success', 'Product deleted successfully.');
        $this->redirect(url('dashboard/products'));
    }
}
