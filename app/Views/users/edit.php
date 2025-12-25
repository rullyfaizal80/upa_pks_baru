<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit User</h5>
                <span class="badge bg-secondary"><?= esc($user['username']) ?></span>
            </div>
            <div class="card-body">
                
                <?php if(session()->get('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                        <?php foreach(session()->get('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('users/update/' . $user['id']) ?>" method="post">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?= old('nama', $user['nama']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Username</label>
                        <input type="text" class="form-control bg-light" value="<?= $user['username'] ?>" readonly disabled>
                        <small class="text-muted">Username digenerate sistem dan tidak dapat diubah.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender" class="form-select">
                                <option value="L" <?= $user['gender'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= $user['gender'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenjang</label>
                            <select name="jenjang" class="form-select">
                                <?php 
                                    $jenjangs = ['Muda', 'Pratama'];
                                    foreach($jenjangs as $j):
                                ?>
                                    <option value="<?= $j ?>" <?= $user['jenjang'] == $j ? 'selected' : '' ?>><?= $j ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block">Role (Hak Akses)</label>
                        <div class="d-flex flex-wrap gap-3">
                            <?php foreach($roles as $role): ?>
                                <div class="form-check">
                                    <?php $isChecked = in_array($role['id'], $user_role_ids) ? 'checked' : ''; ?>
                                    
                                    <input class="form-check-input" type="checkbox" name="role[]" value="<?= $role['id'] ?>" id="role_<?= $role['id'] ?>" <?= $isChecked ?>>
                                    <label class="form-check-label" for="role_<?= $role['id'] ?>">
                                        <?= ucfirst($role['role_name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('users') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Update Data</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>