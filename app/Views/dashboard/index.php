<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <h2 class="h4">Selamat Datang, <?= session()->get('nama') ?>!</h2>
        <p class="text-muted">Anda login sebagai: 
            <?php foreach(session()->get('roles') as $role): ?>
                <span class="badge bg-primary"><?= $role ?></span>
            <?php endforeach; ?>
        </p>
        <hr>
        <div class="alert alert-info">
            Pilih menu di samping untuk mulai mengelola data.
        </div>
    </div>
</div>
<?= $this->endSection() ?>