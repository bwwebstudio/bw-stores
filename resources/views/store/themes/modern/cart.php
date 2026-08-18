<?php

use App\Core\View;
View::layout('layouts.storefront');

View::section('title'); ?>Shopping Cart — <?= e($store['name']) ?><?php View::endSection();

View::section('content'); ?>

<div class="bg-light py-4 border-bottom mb-4">
    <div class="container">
        <h2 class="fw-800 text-dark mb-0">Shopping Cart</h2>
    </div>
</div>

<div class="container mb-5">
    <?php if (empty($cart)): ?>
        <div class="card text-center py-5 border-0 bg-light">
            <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
            <h4 class="fw-700 mt-3">Your cart is empty</h4>
            <p class="text-muted small">Looks like you haven't added any items to your cart yet.</p>
            <div>
                <a href="<?= url('store/' . $store['slug'] . '/products') ?>" class="btn btn-store-primary px-4 py-2">
                    Start Shopping
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Left 8 Columns: Cart Items Table -->
            <div class="col-lg-8">
                <div class="card p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $subtotal = 0;
                                foreach ($cart as $key => $item):
                                    $itemTotal = $item['price'] * $item['quantity'];
                                    $subtotal += $itemTotal;
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($item['image'])): ?>
                                                <img src="<?= url($item['image']) ?>" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                                                    <i class="bi bi-image"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <a href="<?= url('store/' . $store['slug'] . '/product/' . $item['slug']) ?>" class="fw-700 text-dark text-decoration-none">
                                                    <?= e($item['name']) ?>
                                                </a>
                                                <?php if (!empty($item['variant_name'])): ?>
                                                    <div class="text-muted text-xs"><?= e($item['variant_name']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>₹<?= number_format($item['price'], 2) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <form method="POST" action="<?= url('store/' . $store['slug'] . '/cart/update') ?>" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="item_key" value="<?= e($key) ?>">
                                                <input type="hidden" name="action" value="decrease">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary px-2 py-0">-</button>
                                            </form>
                                            <span class="px-2 fw-700"><?= $item['quantity'] ?></span>
                                            <form method="POST" action="<?= url('store/' . $store['slug'] . '/cart/update') ?>" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="item_key" value="<?= e($key) ?>">
                                                <input type="hidden" name="action" value="increase">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary px-2 py-0">+</button>
                                            </form>
                                        </div>
                                    </td>
                                    <td class="fw-700">₹<?= number_format($itemTotal, 2) ?></td>
                                    <td class="text-end">
                                        <form method="POST" action="<?= url('store/' . $store['slug'] . '/cart/update') ?>" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="item_key" value="<?= e($key) ?>">
                                            <input type="hidden" name="action" value="remove">
                                            <button type="submit" class="btn btn-sm text-danger border-0 bg-transparent">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right 4 Columns: Summary & Coupon -->
            <div class="col-lg-4">
                <!-- Coupon Box -->
                <div class="card p-3 mb-3">
                    <h6 class="fw-700 mb-2">Have a Promo Code?</h6>
                    <form method="POST" action="<?= url('store/' . $store['slug'] . '/cart/coupon') ?>">
                        <?= csrf_field() ?>
                        <div class="input-group">
                            <input type="text" name="coupon_code" class="form-control text-uppercase" placeholder="Enter coupon code" required>
                            <button type="submit" class="btn btn-dark">Apply</button>
                        </div>
                    </form>
                </div>

                <!-- Order Calculation -->
                <div class="card p-4">
                    <h5 class="fw-700 mb-3">Order Summary</h5>

                    <?php
                    $appliedCoupon = session()->get("coupon_{$store['id']}");
                    $discount = (float)($appliedCoupon['discount_amount'] ?? 0);
                    $finalTotal = max(0, $subtotal - $discount);
                    ?>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-600">₹<?= number_format($subtotal, 2) ?></span>
                    </div>

                    <?php if ($discount > 0): ?>
                    <div class="d-flex justify-content-between py-2 border-bottom text-success">
                        <span>Coupon (<?= e($appliedCoupon['code']) ?>):</span>
                        <span>-₹<?= number_format($discount, 2) ?></span>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Estimated Shipping:</span>
                        <span class="text-success fw-600">FREE</span>
                    </div>

                    <div class="d-flex justify-content-between py-3 fs-5 fw-800 text-dark">
                        <span>Total:</span>
                        <span class="text-store-primary">₹<?= number_format($finalTotal, 2) ?></span>
                    </div>

                    <a href="<?= url('store/' . $store['slug'] . '/checkout') ?>" class="btn btn-store-primary btn-lg w-100 py-3 fw-700 shadow">
                        Proceed to Checkout <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php View::endSection(); ?>
