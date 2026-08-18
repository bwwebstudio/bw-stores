<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Unavailable — <?= e($store['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="text-center p-4" style="max-width: 500px;">
        <div class="rounded-circle bg-warning-subtle text-warning mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 2rem;">
            ⚠️
        </div>
        <h3 class="fw-800 text-dark mb-2">Store Temporarily Offline</h3>
        <p class="text-muted mb-4"><?= e($reason ?? 'The store is currently undergoing administrative review or maintenance. Please check back later.') ?></p>
        <a href="<?= url('/') ?>" class="btn btn-outline-secondary btn-sm">Visit BW Store Platform</a>
    </div>
</body>
</html>
