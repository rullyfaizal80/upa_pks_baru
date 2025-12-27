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
        
        /* Sidebar Styling (Desktop Default) */
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

        /* --- CSS RESPONSIVE (TAMPILAN HP) --- */
        @media (max-width: 768px) {
            
            /* 1. LOGIKA SIDEBAR HP */
            .sidebar {
                margin-left: -250px; /* Sembunyi by default */
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 1050; /* Z-index tinggi agar di atas konten */
                height: 100vh; /* Full height layar */
                
                /* PERBAIKAN: Agar bisa discroll */
                overflow-y: auto; 
                padding-bottom: 60px; /* Tambahan ruang bawah agar logout tidak kepotong browser nav */
            }
            
            .sidebar.active {
                margin-left: 0; /* Muncul saat aktif */
                box-shadow: 5px 0 15px rgba(0,0,0,0.3); /* Bayangan agar terlihat mengambang */
            }

            /* 2. PERBAIKAN: Hapus Logo & Judul Sidebar di Mode HP */
            /* Karena sudah ada di Navbar Atas */
            .sidebar .sidebar-header, 
            .sidebar hr {
                display: none !important;
            }

            /* Tambahkan sedikit padding atas di menu karena header hilang */
            .sidebar nav {
                margin-top: 10px !important;
            }

            /* 3. LOGIKA TABEL FREEZE (STICKY COLUMN) */
            .col-no { display: none; }
            .sticky-col {
                position: sticky;
                left: 0;
                background-color: #fff !important;
                z-index: 10;
                box-shadow: 2px 0 5px -2px rgba(0,0,0,0.2);
                border-right: 1px solid #dee2e6;
                font-size: 0.9rem;
                max-width: 150px;
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

<nav class="navbar navbar-dark bg-dark d-md-none shadow-sm sticky-top">
  <div class="container-fluid">
    <span class="navbar-brand mb-0 h1 d-flex align-items-center">
    
    <img src="<?= base_url('assets/img/pks.png') ?>" alt="Logo" width="35" height="35" class="d-inline-block me-2">
    
    <div class="d-flex flex-column" style="line-height: 1.1;">
        <span class="fw-bold" style="font-size: 1.1rem;">Aplikasi UPA</span>
        
        <small class="text-white-50 fw-normal" style="font-size: 0.75rem;">
            Halo, <?= session()->get('nama') ?? 'User' ?>
        </small>
    </div>

</span>
    <button class="btn btn-outline-light border-0" type="button" id="sidebarToggle">
        <i class="bi bi-list fs-4"></i>
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
            
            <?php if (has_role('ketua', $my_roles) || has_role('pembina', $my_roles) || has_role('sekertaris', $my_roles) || has_role('admin', $my_roles)) : ?>      
                <div class="text-uppercase small text-secondary px-3 mt-4 mb-1 fw-bold" style="font-size: 0.7rem;">Pembinaan</div>
                <?php if (has_role('pembina', $my_roles) || has_role('sekertaris', $my_roles)) : ?>
                    <a href="<?= base_url('kegiatan') ?>">
                        <i class="bi bi-journal-check me-2"></i> Pelaksanaan UPA
                    </a>
                <?php endif; ?>
                <a href="<?= base_url('pembina') ?>" class="<?= uri_string() == 'pembina' ? 'active' : '' ?>">
                    <i class="bi bi-eye me-2"></i> Monitoring Anggota
                </a> 
                <?php if (has_role('ketua', $my_roles) || has_role('admin', $my_roles)) : ?>        
                    <a href="<?= base_url('pembina/laporan-ketua') ?>">
                        <i class="bi bi-file-earmark-bar-graph me-2"></i> Laporan Bulanan
                    </a>
                    <a href="<?= base_url('statistik') ?>" >            
                        <i class="bi bi-graph-up-arrow me-2"></i> Laporan Pelaksanaan
                    </a>
                <?php endif; ?>        
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

            <a href="<?= base_url('logout') ?>" class="text-danger mt-1 mb-4"> <i class="bi bi-box-arrow-left me-2"></i> Logout
            </a>

        </nav>
        
        <div class="flex-grow-1"></div>
    </div>

    <div class="content">
        <div id="sidebarOverlay" class="d-md-none" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:1040;"></div>

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
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        sidebar.classList.toggle('active');
        // Toggle Overlay display
        if (sidebar.classList.contains('active')) {
            overlay.style.display = 'block';
        } else {
            overlay.style.display = 'none';
        }
    }

    // Toggle Button Click
    toggleBtn?.addEventListener('click', function(e) {
        e.stopPropagation(); // Mencegah event bubbling
        toggleSidebar();
    });

    // Close sidebar when clicking overlay (outside menu)
    overlay?.addEventListener('click', function() {
        sidebar.classList.remove('active');
        overlay.style.display = 'none';
    });
</script>

</body>
</html>