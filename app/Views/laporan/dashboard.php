<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
    <h3 class="mt-4">Laporan Yaumiyah</h3>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Laporan Yaumiyah</li>
    </ol>

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
        <a href="<?= base_url('laporan/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Isi Laporan Baru
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Periode Pekan</th>
                            <th>Tgl UPA</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporan)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-info-circle display-6 d-block mb-2"></i>
                                    Belum ada laporan di bulan ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($laporan as $row): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-primary">
                                        <?= formatTanggalIndo($row['periode_mulai']) ?>
                                    </div>
                                    <div class="small text-muted">s.d <?= formatTanggalIndo($row['periode_selesai']) ?></div>
                                </td>
                                <td><?= formatTanggalIndo($row['tanggal_upa']) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-success rounded-pill">Selesai</span>
                                </td>
                                <td>
            <div class="d-flex gap-2 justify-content-center">
                
                <?php 
                            // Ambil Bulan Laporan & Bulan Sekarang
                            $bulanLaporan = date('Y-m', strtotime($row['periode_mulai']));
                            $bulanIni     = date('Y-m');
                        ?>

                        <?php if ($bulanLaporan == $bulanIni): ?>
                            
                            <a href="/laporan/edit/<?= $row['id'] ?>" class="btn btn-warning btn-sm fw-bold text-white" title="Edit Laporan">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <button type="button" class="btn btn-danger btn-sm fw-bold" onclick="konfirmasiHapus(<?= $row['id'] ?>)" title="Hapus Laporan">
                                <i class="bi bi-trash"></i>
                            </button>

                        <?php else: ?>
                            
                            <button class="btn btn-secondary btn-sm" disabled title="Edit Terkunci (Laporan Arsip)">
                                <i class="bi bi-lock-fill"></i>
                            </button>

                            <button class="btn btn-secondary btn-sm" disabled title="Hapus Terkunci (Laporan Arsip)">
                                <i class="bi bi-lock-fill"></i>
                            </button>

                        <?php endif; ?>
                
            </div>
        </td>
                            </tr>
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
function konfirmasiHapus(id) {
    // Set action form dinamis berdasarkan ID
    const form = document.getElementById('formHapusLaporan');
    form.action = '/laporan/delete/' + id;
    
    // Tampilkan Modal
    const myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    myModal.show();
}
</script>

<?= $this->endSection() ?>