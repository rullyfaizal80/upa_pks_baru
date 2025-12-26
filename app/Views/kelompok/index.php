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
                        <th width="5%">No</th>
                        <th>Nama Kelompok</th>
                        <th>Pembina</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kelompok as $index => $k) : ?>
                    <tr>
                        <td><?= $index + 1 + (10 * ($pager->getCurrentPage('kelompok') - 1)) ?></td>
                        <td>
                            <strong><?= esc($k['nama_kelompok']) ?></strong>
                            <div class="small text-muted">0 Anggota (Segera)</div>
                        </td>
                        <td>
                            <i class="bi bi-person-badge text-primary"></i> <?= esc($k['nama_pembina']) ?>
                        </td>
                        <td>
                            <a href="<?= base_url('kelompok/manage/' . $k['id']) ?>" class="btn btn-info btn-sm text-white">
                                <i class="bi bi-people"></i> Anggota
                            </a>
                            <a href="#" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
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