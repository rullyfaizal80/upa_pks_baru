<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Kelompok</h5>
                <span class="badge bg-secondary">ID: <?= $kelompok['id'] ?></span>
            </div>
            <div class="card-body">
                
                <?php if(session()->get('errors')): ?>
                    <div class="alert alert-danger small p-2">
                        <?= implode('<br>', session()->get('errors')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('kelompok/update/' . $kelompok['id']) ?>" method="post">
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Kelompok</label>
                        <input type="text" name="nama_kelompok" class="form-control" 
                               value="<?= old('nama_kelompok', $kelompok['nama_kelompok']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pembina</label>
                            <select name="pembina_id" class="form-select" required>
                                <option value="">-- Pilih Pembina --</option>
                                <?php foreach($pembinas as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $p['id'] == $kelompok['pembina_id'] ? 'selected' : '' ?>>
                                        <?= $p['nama'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sekertaris</label>
                            <select name="sekertaris_id" class="form-select" required>
                                <option value="">-- Pilih Sekertaris --</option>
                                <?php foreach($sekertaris as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= $s['id'] == $kelompok['sekertaris_id'] ? 'selected' : '' ?>>
                                        <?= $s['nama'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="<?= base_url('kelompok') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Update Data</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>