<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 text-center py-5">
                <div class="card-body">
                    <div class="mb-4">
                        <span class="bg-warning bg-opacity-10 text-warning p-4 rounded-circle">
                            <i class="bi bi-shield-lock-fill display-4"></i>
                        </span>
                    </div>

                    <h2 class="h4 fw-bold text-dark">Akses Belum Tersedia</h2>
                    
                    <p class="text-secondary mb-4 px-4">
                        Mohon maaf, Anda belum dapat mengisi Laporan Yaumiyah karena akun Anda 
                        <strong>belum terdaftar dalam Kelompok (Halaqah) manapun</strong>.
                    </p>

                    <div class="alert alert-info d-inline-block text-start small mb-4">
                        <i class="bi bi-info-circle me-1"></i> <strong>Solusi:</strong><br>
                        Silakan hubungi <b>Admin</b> atau <b>Ketua Kelompok</b> Anda agar akun Anda segera dimasukkan ke dalam kelompok yang sesuai.
                    </div>

                    <div>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-primary px-4">
                            <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>