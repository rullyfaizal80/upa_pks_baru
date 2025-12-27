<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4">
    
    <h3 class="mt-4">Laporan Pelaksanaan UPA</h3>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Laporan UPA - <?= $kelompok['nama_kelompok'] ?></li>
    </ol>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="small text-muted">Tahun</label>
                    <select name="tahun" class="form-select">
                        <?php for ($y = date('Y'); $y >= date('Y') - 2; $y--): ?>
                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small text-muted">Bulan</label>
                    <select name="bulan" class="form-select">
                        <?php
                        $bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni','Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        ?>
                        <?php foreach ($bulanIndo as $i => $b): ?>
                            <option value="<?= $i ?>" <?= $bulan == $i ? 'selected' : '' ?>><?= $b ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 pt-4">
                    <button class="btn btn-secondary w-100"><i class="bi bi-filter"></i> Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
        <a href="<?= base_url('kegiatan/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Isi Laporan Baru
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">Tanggal / Pekan</th>
                            <th>Teknis & Pembina</th>
                            <th>Kehadiran</th>
                            <th>Materi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporan)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-info-circle display-6 d-block mb-2"></i>
                                    Belum ada laporan di periode ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($laporan as $row): ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-primary">
                                        <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                    </div>
                                    <div class="small text-muted">Pekan ke-<?= date('W', strtotime($row['tanggal'])) ?></div>
                                </td>

                                <td>
                                    <?php if($row['teknis_pelaksanaan'] == 'Offline'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success mb-1">Offline</span>
                                    <?php elseif($row['teknis_pelaksanaan'] == 'Online'): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info mb-1">Online</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning mb-1">Hybrid</span>
                                    <?php endif; ?>
                                    
                                    <div class="small mt-1">
                                        <?php if($row['is_pembina_hadir']): ?>
                                            <i class="bi bi-check-circle-fill text-success"></i> Pembina Hadir
                                        <?php else: ?>
                                            <i class="bi bi-x-circle-fill text-danger"></i> Pembina Absen
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="fw-bold"><?= $row['total_hadir'] ?> <span class="text-muted fw-normal">/ <?= $row['total_anggota'] ?></span></div>
                                    <?php if(!empty($row['nama_tidak_hadir'])): ?>
                                        <button type="button" class="btn btn-link btn-sm p-0 text-danger text-decoration-none" 
                                                data-bs-toggle="popover" title="Tidak Hadir" 
                                                data-bs-content="<?= esc($row['nama_tidak_hadir']) ?>">
                                            <small>Lihat Absen</small>
                                        </button>
                                    <?php else: ?>
                                        <small class="text-success">Lengkap</small>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                        <?= esc($row['materi']) ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        
                                        <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $row['id'] ?>" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <?php 
                                            // LOGIKA DEADLINE: TANGGAL 4 BULAN BERIKUTNYA
                                            $tglLaporan = $row['tanggal'];
                                            $tahunLap   = date('Y', strtotime($tglLaporan));
                                            $bulanLap   = date('m', strtotime($tglLaporan));
                                            
                                            // Deadline = Tgl 4 bulan depannya
                                            $deadline   = date('Y-m-d', strtotime("$tahunLap-$bulanLap-01 +1 month +3 days"));
                                            $today      = date('Y-m-d');
                                            
                                            // Cek apakah Terkunci?
                                            $isLocked   = ($today > $deadline);
                                        ?>

                                        <?php if (!$isLocked): ?>
                                            <a href="/kegiatan/edit/<?= $row['id'] ?>" class="btn btn-warning btn-sm fw-bold text-white" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <button type="button" class="btn btn-danger btn-sm fw-bold" onclick="konfirmasiHapus(<?= $row['id'] ?>)" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled title="Terkunci (Lewat Deadline)">
                                                <i class="bi bi-lock-fill"></i>
                                            </button>

                                            <button class="btn btn-secondary btn-sm" disabled title="Terkunci (Lewat Deadline)">
                                                <i class="bi bi-lock-fill"></i>
                                            </button>
                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalDetail<?= $row['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Detail Laporan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Tanggal:</strong> <?= date('d F Y', strtotime($row['tanggal'])) ?></p>
                                            <p><strong>Materi:</strong><br><?= nl2br(esc($row['materi'])) ?></p>
                                            <p><strong>Tidak Hadir:</strong><br><?= $row['nama_tidak_hadir'] ? nl2br(esc($row['nama_tidak_hadir'])) : '-' ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Hapus Laporan?</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus laporan ini? 
                <br><small class="text-danger">Data yang dihapus tidak bisa dikembalikan.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusLaporan" action="" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Inisialisasi Popover (untuk info absen)
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl)
    })

    // Fungsi Konfirmasi Hapus
    function konfirmasiHapus(id) {
        // Set action form dinamis berdasarkan ID
        const form = document.getElementById('formHapusLaporan');
        // Pastikan route ini sesuai dengan Routes.php Anda
        form.action = '/kegiatan/delete/' + id;
        
        // Tampilkan Modal
        const myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        myModal.show();
    }
</script>

<?= $this->endSection() ?>