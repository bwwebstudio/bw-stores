<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= e($order['order_number']) ?> — <?= e($order['store_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; font-family: 'Inter', sans-serif; }
        .invoice-card { max-width: 800px; margin: 2rem auto; background: #fff; border-radius: 12px; padding: 3rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        @media print {
            body { background: #fff; }
            .invoice-card { box-shadow: none; padding: 0; margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print text-center pt-4">
        <button onclick="window.print()" class="btn btn-primary btn-sm me-2"><i class="bi bi-printer"></i> Print Invoice</button>
        <button onclick="window.close()" class="btn btn-outline-secondary btn-sm">Close</button>
    </div>

    <div class="invoice-card">
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div>
                <h3 class="fw-800 text-dark mb-1"><?= e($order['store_name']) ?></h3>
                <p class="text-muted small mb-0">Official Customer Invoice</p>
            </div>
            <div class="text-end">
                <h4 class="fw-700 text-primary mb-1">INVOICE</h4>
                <div class="fw-600">#<?= e($order['order_number']) ?></div>
                <div class="text-muted text-xs">Date: <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <div class="text-muted text-xs uppercase mb-1">Billed & Shipped To:</div>
                <div class="fw-700"><?= e($order['customer_name']) ?></div>
                <div class="small text-secondary">
                    <?= nl2br(e($order['shipping_address'])) ?><br>
                    <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> - <?= e($order['shipping_postal_code']) ?><br>
                    Phone: <?= e($order['customer_mobile']) ?><br>
                    Email: <?= e($order['customer_email']) ?>
                </div>
            </div>
            <div class="col-6 text-end">
                <div class="text-muted text-xs uppercase mb-1">Payment Details:</div>
                <div class="fw-600">Method: <?= e($order['payment_method']) ?></div>
                <div class="small">Status: <span class="badge bg-<?= $order['payment_status'] === 'PAID' ? 'success' : 'warning text-dark' ?>"><?= e($order['payment_status']) ?></span></div>
                <div class="small">Fulfillment: <span class="badge bg-primary"><?= e($order['order_status']) ?></span></div>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Item Description</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td>
                            <div class="fw-600"><?= e($item['product_name']) ?></div>
                            <?php if (!empty($item['variant_name'])): ?>
                                <div class="text-muted text-xs"><?= e($item['variant_name']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>₹<?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td class="text-end fw-700">₹<?= number_format($item['total'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end mb-4">
            <div class="col-5">
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Subtotal:</span>
                    <span>₹<?= number_format($order['subtotal'], 2) ?></span>
                </div>
                <?php if ($order['discount'] > 0): ?>
                <div class="d-flex justify-content-between py-1 border-bottom small text-success">
                    <span>Discount:</span>
                    <span>-₹<?= number_format($order['discount'], 2) ?></span>
                </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Shipping:</span>
                    <span><?= $order['shipping'] > 0 ? '₹' . number_format($order['shipping'], 2) : 'FREE' ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 fw-800 fs-5 text-dark">
                    <span>Total Paid:</span>
                    <span class="text-primary">₹<?= number_format($order['total'], 2) ?></span>
                </div>
            </div>
        </div>

        <div class="border-top pt-3 text-center text-muted text-xs">
            Thank you for your purchase with <?= e($order['store_name']) ?>! Powered by BW Store SaaS.
        </div>
    </div>

</body>
</html>
