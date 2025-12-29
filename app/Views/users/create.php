<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Tambah User Baru</h5>
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

                <div class="alert alert-info d-flex align-items-center mb-4">
                    <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                    <div>
                        <strong>Info Sistem:</strong><br>
                        Username akan digenerate otomatis dari Nama (NamaDepan + NamaBelakang).<br>
                        Password default adalah <strong>123456</strong>.
                    </div>
                </div>

                <form action="<?= base_url('users/store') ?>" method="post">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="<?= old('nama') ?>" placeholder="Contoh: Rully Faizal" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender" class="form-select">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenjang</label>
                            <select name="jenjang" class="form-select">                               
                                <option value="Muda">Muda</option>
                                <option value="Pratama">Pratama</option>                               
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block">Role (Hak Akses) <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-3">
                            <?php foreach($roles as $role): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="role[]" value="<?= $role['id'] ?>" id="role_<?= $role['id'] ?>">
                                    <label class="form-check-label" for="role_<?= $role['id'] ?>">
                                        <?= ucfirst($role['role_name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('users') ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan User</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>