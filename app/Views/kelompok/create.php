<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Buat Kelompok Baru</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('kelompok/store') ?>" method="post">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Kelompok</label>
                        <input type="text" name="nama_kelompok" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pilih Pembina</label>
                            <select name="pembina_id" class="form-select" required>
                                <option value="">-- Pilih Pembina --</option>
                                <?php foreach($pembinas as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= $p['nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text small">Hanya role <b>Pembina</b></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pilih Sekertaris</label>
                            <select name="sekertaris_id" class="form-select" required>
                                <option value="">-- Pilih Sekertaris --</option>
                                <?php foreach($sekertaris as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                             <div class="form-text small">Hanya role <b>Sekertaris</b></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Simpan Kelompok</button>
                    <a href="<?= base_url('kelompok') ?>" class="btn btn-secondary mt-3">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>