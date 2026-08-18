<?php

namespace App\Controllers\Store;

use App\Controllers\Controller;
use App\Core\Request;
use App\Models\Store;
use App\Models\StoreSetting;
use App\Models\Product;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;

class StoreFrontController extends Controller
{
    private Store $storeModel;
    private StoreSetting $settingModel;
    private Product $productModel;
    private Category $categoryModel;
    private Coupon $couponModel;
    private Order $orderModel;

    public function __construct($app)
    {
        parent::__construct($app);
        $db = $this->app->getDatabase();
        $this->storeModel = new Store($db);
        $this->settingModel = new StoreSetting($db);
        $this->productModel = new Product($db);
        $this->categoryModel = new Category($db);
        $this->couponModel = new Coupon($db);
        $this->orderModel = new Order($db);
    }

    private function getStoreContext(string $slug): array
    {
        $store = $this->storeModel->findBySlug($slug);
        if (!$store) {
            $this->app->getRouter()->send404();
            exit;
        }

        if ($store['status'] !== 'active') {
            $this->view('store.suspended', ['store' => $store, 'reason' => 'Store is currently offline or under maintenance.']);
            exit;
        }

        // Verify Merchant's SaaS Subscription is Active or in 7-day Trial and not expired
        $db = $this->app->getDatabase();
        $sub = $db->fetchOne("SELECT * FROM subscriptions WHERE merchant_id = ? ORDER BY id DESC LIMIT 1", [$store['merchant_id']]);
        if (!$sub || !in_array($sub['status'], ['active', 'trialing']) || (!empty($sub['current_period_end']) && strtotime($sub['current_period_end']) < time())) {
            $this->view('store.suspended', ['store' => $store, 'reason' => 'This store subscription is currently expired or awaiting renewal.']);
            exit;
        }

        $settings = $this->settingModel->findByStoreId($store['id']) ?? [];
        $categories = $this->categoryModel->getAllForMerchant($store['merchant_id'], true);
        $cart = session()->get("cart_{$store['id']}", []);

        return [
            'store'      => $store,
            'settings'   => $settings,
            'categories' => $categories,
            'cart'       => $cart,
            'cartCount'  => array_sum(array_column($cart, 'quantity')),
            'theme'      => $settings['theme_name'] ?? 'modern',
        ];
    }

    public function home(Request $request, string $slug): void
    {
        $ctx = $this->getStoreContext($slug);
        $merchantId = (int)$ctx['store']['merchant_id'];

        $featuredProducts = $this->productModel->getAllForMerchant($merchantId, ['status' => 'published', 'is_featured' => 1], 1, 8)['data'];
        $newArrivals = $this->productModel->getAllForMerchant($merchantId, ['status' => 'published'], 1, 8)['data'];

        $this->view('store.themes.' . $ctx['theme'] . '.home', array_merge($ctx, [
            'featuredProducts' => $featuredProducts,
            'newArrivals'      => $newArrivals,
        ]));
    }

    public function products(Request $request, string $slug): void
    {
        $ctx = $this->getStoreContext($slug);
        $merchantId = (int)$ctx['store']['merchant_id'];
        $page = max(1, (int)$request->query('page', 1));

        $filters = [
            'search'      => sanitize_input($request->query('search')),
            'category_id' => $request->query('category_id'),
            'status'      => 'published',
        ];

        $productsData = $this->productModel->getAllForMerchant($merchantId, $filters, $page, 12);

        $this->view('store.themes.' . $ctx['theme'] . '.products', array_merge($ctx, [
            'products' => $productsData['data'],
            'total'    => $productsData['total'],
            'page'     => $productsData['page'],
            'pages'    => $productsData['pages'],
            'filters'  => $filters,
        ]));
    }

    public function product(Request $request, string $slug, string $productSlug): void
    {
        $ctx = $this->getStoreContext($slug);
        $merchantId = (int)$ctx['store']['merchant_id'];

        $product = $this->productModel->findBySlugAndMerchant($productSlug, $merchantId);
        if (!$product || $product['status'] !== 'published') {
            $this->app->getRouter()->send404();
            exit;
        }

        $relatedProducts = $this->productModel->getAllForMerchant($merchantId, [
            'category_id' => $product['category_id'],
            'status'      => 'published'
        ], 1, 4)['data'];

        $this->view('store.themes.' . $ctx['theme'] . '.product_detail', array_merge($ctx, [
            'product'         => $product,
            'relatedProducts' => array_filter($relatedProducts, fn($p) => $p['id'] !== $product['id']),
        ]));
    }

    public function category(Request $request, string $slug, string $categorySlug): void
    {
        $ctx = $this->getStoreContext($slug);
        $merchantId = (int)$ctx['store']['merchant_id'];

        $category = $this->categoryModel->findBySlugAndMerchant($categorySlug, $merchantId);
        if (!$category) {
            $this->app->getRouter()->send404();
            exit;
        }

        $page = max(1, (int)$request->query('page', 1));
        $productsData = $this->productModel->getAllForMerchant($merchantId, [
            'category_id' => $category['id'],
            'status'      => 'published'
        ], $page, 12);

        $this->view('store.themes.' . $ctx['theme'] . '.category', array_merge($ctx, [
            'category' => $category,
            'products' => $productsData['data'],
            'total'    => $productsData['total'],
            'page'     => $productsData['page'],
            'pages'    => $productsData['pages'],
        ]));
    }

    public function cart(Request $request, string $slug): void
    {
        $ctx = $this->getStoreContext($slug);
        $this->view('store.themes.' . $ctx['theme'] . '.cart', $ctx);
    }

    public function addToCart(Request $request, string $slug): void
    {
        $ctx = $this->getStoreContext($slug);
        $storeId = (int)$ctx['store']['id'];
        $merchantId = (int)$ctx['store']['merchant_id'];

        $productId = (int)$request->input('product_id');
        $variantId = $request->input('variant_id') ? (int)$request->input('variant_id') : null;
        $qty = max(1, (int)$request->input('quantity', 1));

        $product = $this->productModel->findByIdAndMerchant($productId, $merchantId);
        if (!$product || $product['status'] !== 'published') {
            if ($request->isAjax()) {
                $this->json(['success' => false, 'message' => 'Product unavailable.'], 400);
            }
            flash('error', 'Product unavailable.');
            $this->redirect(url("store/{$slug}"));
            return;
        }

        $itemKey = $variantId ? "{$productId}_{$variantId}" : "{$productId}";
        $cart = session()->get("cart_{$storeId}", []);

        $price = (float)$product['price'];
        $variantName = null;
        $sku = $product['sku'];

        if ($variantId && !empty($product['variants'])) {
            foreach ($product['variants'] as $v) {
                if ((int)$v['id'] === $variantId) {
                    $price = (float)$v['price'];
                    $variantName = $v['name'];
                    $sku = $v['sku'] ?: $sku;
                    break;
                }
            }
        }

        $image = !empty($product['images'][0]) ? $product['images'][0] : null;

        if (isset($cart[$itemKey])) {
            $cart[$itemKey]['quantity'] += $qty;
        } else {
            $cart[$itemKey] = [
                'product_id'   => $productId,
                'variant_id'   => $variantId,
                'name'         => $product['name'],
                'slug'         => $product['slug'],
                'variant_name' => $variantName,
                'sku'          => $sku,
                'price'        => $price,
                'quantity'     => $qty,
                'image'        => $image,
            ];
        }

        session()->set("cart_{$storeId}", $cart);

        if ($request->isAjax()) {
            $this->json([
                'success'   => true,
                'message'   => 'Added to cart!',
                'cartCount' => array_sum(array_column($cart, 'quantity')),
            ]);
            return;
        }

        flash('success', 'Item added to your cart!');
        $this->redirect(url("store/{$slug}/cart"));
    }

    public function updateCart(Request $request, string $slug): void
    {
        $ctx = $this->getStoreContext($slug);
        $storeId = (int)$ctx['store']['id'];

        $itemKey = $request->input('item_key');
        $action = $request->input('action'); // 'increase', 'decrease', 'remove'
        $cart = session()->get("cart_{$storeId}", []);

        if (isset($cart[$itemKey])) {
            if ($action === 'increase') {
                $cart[$itemKey]['quantity']++;
            } elseif ($action === 'decrease') {
                $cart[$itemKey]['quantity']--;
                if ($cart[$itemKey]['quantity'] <= 0) {
                    unset($cart[$itemKey]);
                }
            } elseif ($action === 'remove') {
                unset($cart[$itemKey]);
            }
        }

        session()->set("cart_{$storeId}", $cart);

        if ($request->isAjax()) {
            $this->json(['success' => true, 'cartCount' => array_sum(array_column($cart, 'quantity'))]);
            return;
        }

        $this->redirect(url("store/{$slug}/cart"));
    }

    public function applyCoupon(Request $request, string $slug): void
    {
        $ctx = $this->getStoreContext($slug);
        $storeId = (int)$ctx['store']['id'];
        $merchantId = (int)$ctx['store']['merchant_id'];
        $cart = session()->get("cart_{$storeId}", []);

        $code = sanitize_input($request->input('coupon_code'));
        $subtotal = 0.0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $result = $this->couponModel->validateAndCalculate($code, $merchantId, $subtotal);

        if ($result['valid']) {
            session()->set("coupon_{$storeId}", [
                'code'            => $result['coupon']['code'],
                'discount_amount' => $result['discount_amount'],
            ]);
            flash('success', $result['message']);
        } else {
            session()->remove("coupon_{$storeId}");
            flash('error', $result['message']);
        }

        $this->redirect(url("store/{$slug}/cart"));
    }

    public function checkout(Request $request, string $slug): void
    {
        $ctx = $this->getStoreContext($slug);
        $storeId = (int)$ctx['store']['id'];
        $cart = session()->get("cart_{$storeId}", []);

        if (empty($cart)) {
            flash('warning', 'Your cart is empty.');
            $this->redirect(url("store/{$slug}"));
            return;
        }

        $subtotal = 0.0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $appliedCoupon = session()->get("coupon_{$storeId}");
        $discount = (float)($appliedCoupon['discount_amount'] ?? 0.00);
        $shipping = 0.00;
        $total = max(0, $subtotal - $discount + $shipping);

        $merchantUpi = $ctx['settings']['merchant_upi_id'] ?? '';
        $merchantUpiName = $ctx['settings']['merchant_upi_name'] ?? $ctx['store']['name'];
        $customerUpiLink = '';
        $customerQrUrl = '';
        if (!empty($merchantUpi)) {
            $customerUpiLink = "upi://pay?pa=" . urlencode($merchantUpi) . "&pn=" . urlencode($merchantUpiName) . "&am=" . number_format($total, 2, '.', '') . "&cu=INR&tn=" . urlencode("Order at {$ctx['store']['name']}");
            $customerQrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" . urlencode($customerUpiLink);
        }

        $this->view('store.themes.' . $ctx['theme'] . '.checkout', array_merge($ctx, [
            'subtotal'        => $subtotal,
            'discount'        => $discount,
            'shipping'        => $shipping,
            'total'           => $total,
            'appliedCoupon'   => $appliedCoupon,
            'customerUpiLink' => $customerUpiLink,
            'customerQrUrl'   => $customerQrUrl,
        ]));
    }

    public function processCheckout(Request $request, string $slug): void
    {
        $ctx = $this->getStoreContext($slug);
        $storeId = (int)$ctx['store']['id'];
        $merchantId = (int)$ctx['store']['merchant_id'];
        $cart = session()->get("cart_{$storeId}", []);

        if (empty($cart)) {
            flash('error', 'Your cart is empty.');
            $this->redirect(url("store/{$slug}"));
            return;
        }

        // Validate customer form fields
        $customerName = sanitize_input($request->input('customer_name'));
        $customerEmail = sanitize_email($request->input('customer_email'));
        $customerMobile = sanitize_input($request->input('customer_mobile'));
        $shippingAddress = sanitize_input($request->input('shipping_address'));
        $shippingCity = sanitize_input($request->input('shipping_city'));
        $shippingState = sanitize_input($request->input('shipping_state'));
        $shippingPostalCode = sanitize_input($request->input('shipping_postal_code'));
        
        $inputMethod = strtoupper(sanitize_input($request->input('payment_method', 'COD')));
        $paymentMethod = in_array($inputMethod, ['COD', 'UPI', 'ONLINE']) ? $inputMethod : 'COD';
        $customerUtr = sanitize_input($request->input('customer_utr', ''));

        if (empty($customerName) || empty($customerEmail) || empty($customerMobile) || empty($shippingAddress)) {
            $this->backWithErrors(['general' => 'Please fill in all required shipping fields.'], $request->all());
            return;
        }

        // Recalculate ALL prices directly from database (never trust client)
        $subtotal = 0.0;
        $verifiedItems = [];

        foreach ($cart as $item) {
            $prod = $this->productModel->findByIdAndMerchant((int)$item['product_id'], $merchantId);
            if (!$prod) continue;

            $price = (float)$prod['price'];
            $varName = null;
            $sku = $prod['sku'];

            if (!empty($item['variant_id'])) {
                foreach ($prod['variants'] as $v) {
                    if ((int)$v['id'] === (int)$item['variant_id']) {
                        $price = (float)$v['price'];
                        $varName = $v['name'];
                        $sku = $v['sku'] ?: $sku;
                        break;
                    }
                }
            }

            $itemTotal = $price * $item['quantity'];
            $subtotal += $itemTotal;

            $verifiedItems[] = [
                'product_id'   => $prod['id'],
                'variant_id'   => $item['variant_id'] ?? null,
                'name'         => $prod['name'],
                'sku'          => $sku,
                'variant_name' => $varName,
                'price'        => $price,
                'quantity'     => $item['quantity'],
            ];
        }

        // Re-validate coupon server side
        $appliedCoupon = session()->get("coupon_{$storeId}");
        $discount = 0.0;
        $couponCode = null;

        if ($appliedCoupon) {
            $res = $this->couponModel->validateAndCalculate($appliedCoupon['code'], $merchantId, $subtotal);
            if ($res['valid']) {
                $discount = $res['discount_amount'];
                $couponCode = $appliedCoupon['code'];
            }
        }

        $shipping = 0.00;
        $tax = 0.00;
        $finalTotal = max(0, $subtotal - $discount + $shipping + $tax);

        $notes = sanitize_input($request->input('notes', ''));
        if ($paymentMethod === 'UPI' && !empty($customerUtr)) {
            $notes = trim("Customer UPI UTR: {$customerUtr} | " . $notes, " |");
        }

        // Place Order Atomically
        $orderRes = $this->orderModel->createOrder([
            'merchant_id'          => $merchantId,
            'store_id'             => $storeId,
            'customer_name'        => $customerName,
            'customer_email'       => $customerEmail,
            'customer_mobile'      => $customerMobile,
            'shipping_address'     => $shippingAddress,
            'shipping_city'        => $shippingCity,
            'shipping_state'       => $shippingState,
            'shipping_postal_code' => $shippingPostalCode,
            'subtotal'             => $subtotal,
            'discount'             => $discount,
            'tax'                  => $tax,
            'shipping'             => $shipping,
            'total'                => $finalTotal,
            'coupon_code'          => $couponCode,
            'payment_method'       => $paymentMethod,
            'payment_status'       => $paymentMethod === 'ONLINE' ? 'PAID' : 'PENDING',
            'notes'                => $notes,
        ], $verifiedItems);

        // Clear Cart & Coupon from session
        session()->remove("cart_{$storeId}");
        session()->remove("coupon_{$storeId}");

        $this->redirect(url("store/{$slug}/order-success/{$orderRes['order_number']}"));
    }

    public function orderSuccess(Request $request, string $slug, string $orderNumber): void
    {
        $ctx = $this->getStoreContext($slug);
        $order = $this->orderModel->findByOrderNumber($orderNumber);

        if (!$order || (int)$order['store_id'] !== (int)$ctx['store']['id']) {
            $this->app->getRouter()->send404();
            exit;
        }

        $this->view('store.themes.' . $ctx['theme'] . '.order_success', array_merge($ctx, [
            'order' => $order,
        ]));
    }
}
