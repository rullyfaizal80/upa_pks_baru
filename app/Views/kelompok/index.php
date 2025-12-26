<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Kelompok UPA</h5>
        <a href="<?= base_url('kelompok/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Buat Kelompok Baru
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="col-no">No</th>
                        
                        <th class="sticky-col">Nama Kelompok</th>
                        
                        <th>Pembina</th>
                        <th>Sekertaris</th> <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kelompok as $index => $k) : ?>
                    <tr>
                        <td class="col-no"><?= $index + 1 + (10 * ($pager->getCurrentPage('kelompok') - 1)) ?></td>
                        
                        <td class="sticky-col">
                            <div class="fw-bold"><?= esc($k['nama_kelompok']) ?></div>
                            <div class="small text-muted">
                                <i class="bi bi-people-fill"></i> <?= $k['jumlah_anggota'] ?> Anggota
                            </div>
                        </td>
                        
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-badge text-primary me-2"></i> 
                                <?= esc($k['nama_pembina']) ?>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-pen text-warning me-2"></i> 
                                <?= esc($k['nama_sekertaris']) ?>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?= base_url('kelompok/manage/' . $k['id']) ?>" class="btn btn-info btn-sm text-white" title="Kelola Anggota">
                                    <i class="bi bi-people"></i> <span class="d-none d-md-inline">Anggota</span>
                                </a>
                                <a href="<?= base_url('kelompok/edit/' . $k['id']) ?>" class="btn btn-warning btn-sm" title="Edit Kelompok">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?= $pager->links('kelompok', 'bootstrap_pagination') ?>
    </div>
</div>

<?= $this->endSection() ?>