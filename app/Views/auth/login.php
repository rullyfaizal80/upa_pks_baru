<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Aplikasi UPA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { max-width: 400px; width: 100%; border: none; shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="card login-card p-4">
    <div class="card-body">
        <h4 class="card-title text-center mb-4 fw-bold">Login UPA</h4>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger text-sm">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/process') ?>" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Masuk</button>
            </div>
        </form>
    </div>
    <div class="card-footer bg-white text-center mt-3 border-0">
        <small class="text-muted">Aplikasi Pelaporan UPA &copy; <?= date('Y') ?></small>
    </div>
</div>

</body>
</html>