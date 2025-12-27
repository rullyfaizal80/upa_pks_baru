<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h3 class="fw-bold text-primary">Riwayat Laporan UPA</h3>
            <p class="text-muted mb-0">
                Kelompok: <strong><?= $kelompok['nama_kelompok'] ?></strong> 
                &bull; Pembina: <?= $kelompok['nama_pembina'] ?>
                &bull; Sekertaris: <?= $kelompok['nama_sekertaris'] ?? '-' ?>
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="/kegiatan/create" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Buat Laporan Baru
            </a>
        </div>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-4 py-3" width="5%">No</th>
                            <th width="15%">Tanggal</th>
                            <th width="10%">Teknis</th>
                            <th width="15%">Kehadiran Pembina</th>
                            <th width="15%">Kehadiran Anggota</th>
                            <th>Materi / KKP</th>
                            <th class="pe-4 text-end" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($laporan)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-50"></i>
                                    Belum ada laporan kegiatan yang dibuat.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach($laporan as $lap): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-secondary"><?= $no++ ?></td>
                                
                                <td>
                                    <div class="fw-bold text-dark"><?= date('d M Y', strtotime($lap['tanggal'])) ?></div>
                                    <small class="text-muted">Pekan ke-<?= date('W', strtotime($lap['tanggal'])) ?></small>
                                </td>

                                <td>
                                    <?php if($lap['teknis_pelaksanaan'] == 'Offline'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 border border-success">Offline</span>
                                    <?php elseif($lap['teknis_pelaksanaan'] == 'Online'): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 border border-info">Online</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 border border-warning">Hybrid</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if($lap['is_pembina_hadir']): ?>
                                        <span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Hadir</span>
                                    <?php else: ?>
                                        <span class="text-danger fw-bold"><i class="bi bi-x-circle-fill"></i> Tidak Hadir</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="fw-bold fs-5 me-2"><?= $lap['total_hadir'] ?></div>
                                        <div class="text-muted small">
                                            dari <?= $lap['total_anggota'] ?> <br> Anggota
                                        </div>
                                    </div>
                                    <?php 
                                        // Bar Persentase Sederhana
                                        $persen = ($lap['total_anggota'] > 0) ? ($lap['total_hadir'] / $lap['total_anggota']) * 100 : 0;
                                        $warnaBar = ($persen >= 80) ? 'bg-success' : (($persen >= 50) ? 'bg-warning' : 'bg-danger');
                                    ?>
                                    <div class="progress mt-1" style="height: 4px; width: 80px;">
                                        <div class="progress-bar <?= $warnaBar ?>" role="progressbar" style="width: <?= $persen ?>%"></div>
                                    </div>
                                </td>

                                <td>
                                    <span class="text-dark d-block text-truncate" style="max-width: 250px;">
                                        <?= esc($lap['materi']) ?>
                                    </span>
                                </td>

                                <td class="pe-4 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $lap['id'] ?>">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalDetail<?= $lap['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Detail Laporan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-sm table-borderless">
                                                <tr><td width="35%" class="text-muted">Tanggal</td><td class="fw-bold"><?= date('d F Y', strtotime($lap['tanggal'])) ?></td></tr>
                                                <tr><td class="text-muted">Teknis</td><td><?= $lap['teknis_pelaksanaan'] ?></td></tr>
                                                <tr><td class="text-muted">Status Pembina</td><td><?= $lap['is_pembina_hadir'] ? 'Hadir' : 'Tidak Hadir' ?></td></tr>
                                                <tr>
                                                    <td class="text-muted">Kehadiran</td>
                                                    <td>
                                                        <span class="badge bg-primary"><?= $lap['total_hadir'] ?> Hadir</span> 
                                                        dari <?= $lap['total_anggota'] ?> Anggota
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted align-top">Tidak Hadir</td>
                                                    <td class="text-danger fst-italic"><?= empty($lap['nama_tidak_hadir']) ? '-' : nl2br(esc($lap['nama_tidak_hadir'])) ?></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="pt-3"><strong class="d-block mb-1">Materi / KKP:</strong>
                                                        <div class="p-2 bg-light rounded border">
                                                            <?= nl2br(esc($lap['materi'])) ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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

<?= $this->endSection() ?>