<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white text-center py-3">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-shield-lock"></i> Ganti Password</h4>
                </div>
                <div class="card-body p-4">

                    <?php $errors = session()->getFlashdata('errors'); ?>

                    <form action="/ganti-password/update" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Password Lama</label>
                            <div class="input-group has-validation">
                                <input type="password" name="password_lama" 
                                       class="form-control <?= isset($errors['password_lama']) ? 'is-invalid' : '' ?>" 
                                       id="pass_lama" 
                                       value="<?= old('password_lama') ?>" required>
                                
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('pass_lama', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                
                                <div class="invalid-feedback">
                                    <?= isset($errors['password_lama']) ? $errors['password_lama'] : '' ?>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Password Baru</label>
                            <div class="input-group has-validation">
                                <input type="password" name="password_baru" 
                                       class="form-control <?= isset($errors['password_baru']) ? 'is-invalid' : '' ?>" 
                                       id="pass_baru" 
                                       value="<?= old('password_baru') ?>" required>
                                
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('pass_baru', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                
                                <div class="invalid-feedback">
                                    <?= isset($errors['password_baru']) ? $errors['password_baru'] : '' ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Ulangi Password Baru</label>
                            <div class="input-group has-validation">
                                <input type="password" name="konfirmasi_password" 
                                       class="form-control <?= isset($errors['konfirmasi_password']) ? 'is-invalid' : '' ?>" 
                                       id="pass_konf" 
                                       value="<?= old('konfirmasi_password') ?>" required>
                                
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('pass_konf', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                
                                <div class="invalid-feedback">
                                    <?= isset($errors['konfirmasi_password']) ? $errors['konfirmasi_password'] : '' ?>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary fw-bold py-2">
                                SIMPAN PASSWORD BARU
                            </button>
                            <a href="/dashboard" class="btn btn-light text-muted">Batal / Kembali</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = "password";
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>

<?= $this->endSection() ?>