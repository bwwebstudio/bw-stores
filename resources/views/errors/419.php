<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 — Page Expired</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= function_exists('url') ? url('public/assets/css/app.css') : '/public/assets/css/app.css' ?>">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="text-center p-4" style="max-width: 480px;">
        <div class="display-1 fw-800 text-warning mb-2">419</div>
        <h2 class="fw-700 mb-3">Session or Page Expired</h2>
        <p class="text-muted mb-4">Your security verification token has expired or is invalid. Please refresh the page and try again.</p>
        <a href="javascript:history.back()" class="btn btn-primary me-2">Go Back & Refresh</a>
        <a href="<?= function_exists('url') ? url('login') : '/login' ?>" class="btn btn-outline-primary">Return to Login</a>
    </div>
</body>
</html>
