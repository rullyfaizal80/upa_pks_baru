<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Aplikasi UPA</title>
    
    <link rel="shortcut icon" href="<?= base_url('assets/img/favicon.ico') ?>" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f0f2f5; /* Warna background abu-abu muda yang lembut */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08); /* Bayangan halus */
            overflow: hidden;
        }
        .login-header {
            background-color: #fff;
            padding: 2rem 1rem 1rem 1rem;
            text-align: center;
        }
        .logo-img {
            max-height: 80px; /* Atur tinggi logo */
            margin-bottom: 15px;
        }
        .app-title {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0;
        }
        .app-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .btn-login {
            background-color: #d35400; /* Warna Orange PKS (sesuaikan jika perlu) */
            border: none;
            padding: 10px;
            font-weight: 600;
        }
        .btn-login:hover {
            background-color: #e67e22;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <div class="card login-card">
                    
                    <div class="login-header">
                        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo UPA" class="logo-img">
                        
                        <h4 class="app-title">DPC ARCAMANIK</h4>
                        <p class="app-subtitle">Unit Pembinaan Anggota</p>
                    </div>

                    <div class="card-body p-4 pt-2">
                        
                        <?php if(session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger small">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('auth/process') ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label small text-muted">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label small text-muted">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-login text-white">
                                    MASUK APLIKASI
                                </button>
                            </div>

                        </form>
                    </div>
                    
                    <div class="card-footer bg-white text-center py-3 border-0">
                        <small class="text-muted">&copy; <?= date('Y') ?> UPA System v1.0</small>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>