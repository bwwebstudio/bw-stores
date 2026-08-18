<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 — Too Many Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= function_exists('url') ? url('public/assets/css/app.css') : '/public/assets/css/app.css' ?>">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="text-center p-4" style="max-width: 480px;">
        <div class="display-1 fw-800 text-danger mb-2">429</div>
        <h2 class="fw-700 mb-3">Too Many Requests</h2>
        <p class="text-muted mb-4">Rate limit reached for security protection. Please wait a few minutes before trying again.</p>
        <a href="<?= function_exists('url') ? url('/') : '/' ?>" class="btn btn-primary">Return to Homepage</a>
    </div>
</body>
</html>
