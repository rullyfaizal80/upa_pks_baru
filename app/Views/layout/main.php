<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplikasi UPA</title>   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { min-height: 100vh; display: flex; flex-direction: column; background-color: #f8f9fa; }
        .wrapper { display: flex; flex: 1; }
        
        /* Sidebar Styling */
        .sidebar { 
            min-width: 250px; 
            max-width: 250px; 
            background: #212529; 
            color: #fff; 
            min-height: 100vh;
            transition: all 0.3s;
        }
        .sidebar a { 
            color: #adb5bd; 
            text-decoration: none; 
            display: block; 
            padding: 12px 20px; 
            border-left: 3px solid transparent;
        }
        .sidebar a:hover { 
            background: #343a40; 
            color: #fff; 
        }
        .sidebar a.active { 
            background: #343a40; 
            color: #fff; 
            border-left-color: #0d6efd; 
        }
        .sidebar-header {
            padding: 20px;
            background: #1a1e21;
            text-align: center;
        }

        /* Content Area */
        .content { 
            flex: 1; 
            padding: 30px; 
            width: 100%;
        }

        /* --- 2. CSS RESPONSIVE (TAMPILAN HP) --- */
        @media (max-width: 768px) {
            
            /* A. LOGIKA SIDEBAR HP (Sembunyi by default) */
            .sidebar {
                margin-left: -250px;
                position: fixed;
                z-index: 999;
                height: 100%;
            }
            .sidebar.active {
                margin-left: 0;
            }

            /* B. LOGIKA TABEL FREEZE (STICKY COLUMN) */
            
            /* Sembunyikan kolom "No" di HP agar hemat tempat */
            .col-no {
                display: none;
            }

            /* Bekukan (Freeze) kolom "Nama" agar menempel di kiri saat discroll */
            .sticky-col {
                position: sticky;
                left: 0;
                background-color: #fff !important; /* Wajib putih agar tidak transparan */
                z-index: 10;
                box-shadow: 2px 0 5px -2px rgba(0,0,0,0.2); /* Bayangan pemisah */
                border-right: 1px solid #dee2e6;
                
                /* Opsional: Kecilkan font sedikit di HP agar muat */
                font-size: 0.9rem;
                max-width: 150px; /* Batasi lebar nama agar tidak terlalu lebar */
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        }
        .select2-container--bootstrap-5 .select2-selection {
            border-color: #dee2e6; 
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark d-md-none shadow-sm">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1">
        <img src="<?= base_url('assets/img/pks.png') ?>" alt="Logo" width="30" height="30" class="d-inline-block align-text-top me-1">
        App UPA
    </span>
    <button class="btn btn-outline-light" type="button" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>
  </div>
</nav>

<div class="wrapper">
    <div class="sidebar d-flex flex-column" id="sidebar">
        
        <div class="sidebar-header d-flex align-items-center p-3">
            <div class="me-3">
                <img src="<?= base_url('assets/img/pks.png') ?>" alt="Logo UPA" style="width: 45px; height: auto;">
            </div>
            <div>
                <h5 class="mb-0 fw-bold">Aplikasi UPA</h5>
                <small class="text-white-50" style="font-size: 0.8rem;">
                    Halo, <?= session()->get('nama') ?? 'User' ?>
                </small>
            </div>
        </div>
        
        <hr class="text-secondary mt-0 mb-2 mx-3">

        <?php 
            $my_roles = session()->get('roles') ?? []; 
            function has_role($role, $user_roles) {
                return in_array($role, $user_roles);
            }
        ?>

        <nav class="mt-2">
    <a href="<?= base_url('dashboard') ?>" class="<?= uri_string() == 'dashboard' ? 'active' : '' ?>">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>

    <?php if (has_role('admin', $my_roles)) : ?>
        <div class="text-uppercase small text-secondary px-3 mt-4 mb-1 fw-bold" style="font-size: 0.7rem;">Administrator</div>
        <a href="<?= base_url('users') ?>" class="<?= uri_string() == 'users' ? 'active' : '' ?>">
            <i class="bi bi-people me-2"></i> Manajemen User
        </a>
        <a href="<?= base_url('kelompok') ?>" class="<?= uri_string() == 'kelompok' ? 'active' : '' ?>">
            <i class="bi bi-diagram-3 me-2"></i> Data Kelompok
        </a>        
    <?php endif; ?>

    <div class="text-uppercase small text-secondary px-3 mt-4 mb-1 fw-bold" style="font-size: 0.7rem;">Pembinaan</div>
    <?php if (has_role('ketua', $my_roles) || has_role('admin', $my_roles)) : ?>
        <a href="<?= base_url('pembina/laporan-ketua') ?>">
            <i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Bulanan
        </a>
    <?php endif; ?>
    <?php if (has_role('ketua', $my_roles) || has_role('pembina', $my_roles) || has_role('sekertaris', $my_roles)) : ?>      
        <a href="<?= base_url('pembina') ?>" class="<?= uri_string() == 'pembina' ? 'active' : '' ?>">
            <i class="bi bi-eye me-2"></i> Monitoring Anggota
        </a>       
    <?php endif; ?>

    <?php if (has_role('anggota', $my_roles)) : ?>
        <div class="text-uppercase small text-secondary px-3 mt-4 mb-1 fw-bold" style="font-size: 0.7rem;">Aktivitas</div>
        <a href="<?= base_url('laporan/dashboard') ?>" class="<?= uri_string() == 'laporan/dashboard' ? 'active' : '' ?>">
            <i class="bi bi-calendar-check me-2"></i> Laporan Yaumiyah
        </a>
    <?php endif; ?>

    <div class="text-uppercase small text-secondary px-3 mt-4 mb-1 fw-bold" style="font-size: 0.7rem;">Akun</div>
    
    <a href="<?= base_url('ganti-password') ?>" class="<?= uri_string() == 'ganti-password' ? 'active' : '' ?>">
        <i class="bi bi-shield-lock me-2"></i> Ganti Password
    </a>

    <a href="<?= base_url('logout') ?>" class="text-danger mt-1">
        <i class="bi bi-box-arrow-left me-2"></i> Logout
    </a>

</nav>

               
        <div class="flex-grow-1"></div>
    </div>

    <div class="content">
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>

</body>
</html>