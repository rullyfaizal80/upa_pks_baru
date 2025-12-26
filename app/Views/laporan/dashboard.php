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
                                <td class="text-center">
                                    <a href="<?= base_url('laporan/edit/' . $row['id']) ?>" class="btn btn-sm btn-warning text-dark" title="Edit Laporan">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
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

<?= $this->endSection() ?>