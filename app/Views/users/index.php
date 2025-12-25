<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manajemen Users</h5>
        <a href="<?= base_url('users/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Tambah User
        </a>
    </div>
    <div class="card-body">
        
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Gender</th>
                        <th>Jenjang</th>
                        <th>Role</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $index => $user) : ?>
                    <tr>
                        <td><?= $index + 1 + (5 * ($pager->getCurrentPage('users') - 1)) ?></td>
                        <td><?= esc($user['nama']) ?></td>
                        <td><?= esc($user['username']) ?></td>
                        <td><?= esc($user['gender']) ?></td>
                        <td><?= esc($user['jenjang']) ?></td>
                        <td>
                            <?php 
                                $roles = explode(',', $user['role_names'] ?? '');
                                foreach($roles as $role): 
                            ?>
                                <span class="badge bg-secondary"><?= esc($role) ?></span>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                
                                <a href="<?= base_url('users/edit/' . $user['id']) ?>" class="btn btn-warning btn-sm" title="Edit Data">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form action="<?= base_url('users/reset-password/' . $user['id']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-info btn-sm text-white" 
                                            onclick="return confirm('Reset password user ini menjadi 123456?')" 
                                            title="Reset Password ke 123456">
                                        <i class="bi bi-key-fill"></i>
                                    </button>
                                </form>
                                
                                <form action="<?= base_url('users/delete/' . $user['id']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Yakin ingin menghapus user ini? Data tidak bisa dikembalikan.')" 
                                            title="Hapus User">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            <?= $pager->links('users', 'bootstrap_pagination') ?>
        </div>

    </div>
</div>

<style>
    .pagination { margin-bottom: 0; }
    .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; }
</style>

<?= $this->endSection() ?>