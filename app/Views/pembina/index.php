<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <h3 class="mb-4">Dashboard Pembinaan</h3>

    <div class="row">
        <?php if (empty($kelompok_list)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Anda belum memiliki kelompok binaan.
                </div>
            </div>
        <?php else: ?>
            
            <?php foreach($kelompok_list as $k): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-top border-4 border-primary">
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-primary mb-3">
                            <i class="bi bi-people-fill me-2"></i><?= $k['nama_kelompok'] ?>
                        </h5>

                        <div class="mb-3">
                            <table class="table table-sm table-borderless small mb-0">
                                <tr>
                                    <td class="text-muted" width="30%">Pembina</td>
                                    <td class="fw-bold">: <?= $k['nama_pembina'] ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Sekertaris</td>
                                    <td class="fw-bold">: <?= $k['nama_sekertaris'] ?></td>
                                </tr>
                            </table>
                        </div>

                        <div class="bg-light p-2 rounded mb-3" style="max-height: 150px; overflow-y: auto;">
                            <label class="small text-secondary fw-bold mb-2">DAFTAR ANGGOTA (<?= count($k['list_anggota']) ?>):</label>
                            <?php if(empty($k['list_anggota'])): ?>
                                <div class="small text-muted fst-italic">- Belum ada anggota -</div>
                            <?php else: ?>
                                <ul class="list-unstyled small mb-0">
                                    <?php foreach($k['list_anggota'] as $anggota): ?>
                                        <li class="mb-1 border-bottom pb-1">
                                            <i class="bi bi-person me-1 text-secondary"></i> 
                                            <?= $anggota['nama'] ?>
                                            <span class="badge bg-secondary ms-1" style="font-size: 0.6em;"><?= $anggota['jenjang'] ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid">
                            <a href="/pembina/monitoring/<?= $k['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye"></i> Monitor Laporan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>