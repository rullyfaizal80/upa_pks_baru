<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4">
    
    <h3 class="mt-4">Laporan Pelaksanaan UPA</h3>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Laporan UPA</li>
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

    <div class="card mb-4 shadow-sm border-start border-4 border-primary">
        <div class="card-body py-3">
            <form method="get" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="small text-muted fw-bold">Tahun</label>
                    <select name="tahun" class="form-select form-select-sm">
                        <?php for ($y = date('Y'); $y >= date('Y') - 2; $y--): ?>
                            <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small text-muted fw-bold">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm">
                        <?php $bulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni','Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; ?>
                        <?php foreach ($bulanIndo as $i => $b): ?>
                            <option value="<?= $i ?>" <?= $bulan == $i ? 'selected' : '' ?>><?= $b ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 pt-4">
                    <button class="btn btn-primary btn-sm w-100"><i class="bi bi-filter"></i> Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <?php foreach ($dataPerKelompok as $index => $data): ?>
        <?php 
            $kelompok = $data['info']; 
            $laporanList = $data['laporan'];
        ?>

        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-primary mb-1">
                        <i class="bi bi-people-fill me-2"></i> Kelompok: <?= esc($kelompok['nama_kelompok']) ?>
                    </h5>
                    <div class="small text-muted">
                        Pembina: <strong><?= esc($kelompok['nama_pembina']) ?></strong> &bull; 
                        Sekertaris: <strong><?= esc($kelompok['nama_sekertaris'] ?? '-') ?></strong>
                    </div>
                </div>
                <div>
                    <a href="<?= base_url('kegiatan/create?kelompok_id=' . $kelompok['id']) ?>" class="btn btn-sm btn-outline-primary fw-bold">
                        <i class="bi bi-plus-lg"></i> Buat Laporan
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-center" width="5%">No</th>
                                <th>Tanggal / Pekan</th>
                                <th>Teknis & Pembina</th>
                                <th>Kehadiran</th>
                                <th>Materi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($laporanList)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-journal-x display-6 d-block mb-2 opacity-50"></i>
                                        Belum ada laporan untuk kelompok ini di periode terpilih.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($laporanList as $row): ?>
                                <tr>
                                    <td class="text-center text-muted"><?= $no++ ?></td>
                                    
                                    <td>
                                        <div class="fw-bold text-dark"><?= date('d M Y', strtotime($row['tanggal'])) ?></div>
                                        <div class="small text-muted">Pekan ke-<?= date('W', strtotime($row['tanggal'])) ?></div>
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark border mb-1">
                                            <?= $row['teknis_pelaksanaan'] ?>
                                        </span>
                                        <div class="small">
                                            <?php if($row['is_pembina_hadir']): ?>
                                                <i class="bi bi-check-circle-fill text-success"></i> Pembina Hadir
                                            <?php else: ?>
                                                <i class="bi bi-x-circle-fill text-danger"></i> Pembina Absen
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="fw-bold"><?= $row['total_hadir'] ?> <span class="fw-normal text-muted">/ <?= $row['total_anggota'] ?></span></div>
                                        <?php if(!empty($row['nama_tidak_hadir'])): ?>
                                            <small class="text-danger fst-italic">Ada yang absen</small>
                                        <?php else: ?>
                                            <small class="text-success fw-bold">Lengkap</small>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <span class="d-inline-block text-truncate text-secondary" style="max-width: 150px;">
                                            <?= esc($row['materi']) ?>
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <?php 
                                            // LOGIC DEADLINE
                                            $tglLap = $row['tanggal'];
                                            $deadline = date('Y-m-d', strtotime(date('Y-m-01', strtotime($tglLap)) . " +1 month +3 days"));
                                            $isLocked = (date('Y-m-d') > $deadline);
                                        ?>
                                        
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $row['id'] ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                            <?php if (!$isLocked): ?>
                                                <a href="/kegiatan/edit/<?= $row['id'] ?>" class="btn btn-warning btn-sm text-white">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="konfirmasiHapus(<?= $row['id'] ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm" disabled title="Terkunci"><i class="bi bi-lock-fill"></i></button>
                                                <button class="btn btn-secondary btn-sm" disabled title="Terkunci"><i class="bi bi-lock-fill"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalDetail<?= $row['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail Laporan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item"><strong>Tanggal:</strong> <?= date('d F Y', strtotime($row['tanggal'])) ?></li>
                                                    <li class="list-group-item"><strong>Anggota Absen:</strong><br>
                                                        <?= $row['nama_tidak_hadir'] ? nl2br(esc($row['nama_tidak_hadir'])) : '-' ?>
                                                    </li>
                                                    <li class="list-group-item bg-light"><strong>Materi:</strong><br><?= nl2br(esc($row['materi'])) ?></li>
                                                </ul>
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
    <?php endforeach; ?>

</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Hapus Laporan?</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">Yakin hapus data ini? Tidak bisa dikembalikan.</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formHapusLaporan" action="" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function konfirmasiHapus(id) {
        document.getElementById('formHapusLaporan').action = '/kegiatan/delete/' + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>

<?= $this->endSection() ?>