<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Laporan Pelaksanaan UPA</h3>
        <a href="/kegiatan" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="bi bi-pen"></i> Form Laporan Mingguan</h6>
        </div>
        <div class="card-body p-4">
            
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="/kegiatan/store" method="post">
                <?= csrf_field() ?>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <small class="text-muted text-uppercase fw-bold">Kelompok</small>
                            <div class="fw-bold text-primary"><?= $kelompok['nama_kelompok'] ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border">
                            <small class="text-muted text-uppercase fw-bold">Pembina Saat Ini</small>
                            <div class="fw-bold text-dark"><?= $kelompok['nama_pembina'] ?></div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    [cite_start]<label class="form-label fw-bold">Tanggal Pelaksanaan <span class="text-danger">*</span> [cite: 7]</label>
                    <input type="date" name="tanggal" class="form-control" required value="<?= old('tanggal', date('Y-m-d')) ?>">
                    <div class="form-text text-danger">* Hanya boleh mengisi 1 laporan per minggu.</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        [cite_start]<label class="form-label fw-bold">Teknis Pelaksanaan [cite: 110]</label>
                        <select name="teknis" class="form-select" required>
                            <option value="Offline">Offline (Tatap Muka)</option>
                            <option value="Online">Online (Daring)</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        [cite_start]<label class="form-label fw-bold">Kehadiran Pembina [cite: 115]</label>
                        <div class="d-flex gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pembina_hadir" value="1" id="h1" checked>
                                <label class="form-check-label" for="h1">Hadir (1)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pembina_hadir" value="0" id="h0">
                                <label class="form-check-label text-danger" for="h0">Tidak Hadir (0)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        [cite_start]<label class="form-label fw-bold">Jumlah Seluruh Anggota [cite: 121]</label>
                        <input type="number" name="total_anggota" class="form-control" required min="1" placeholder="Contoh: 10">
                    </div>
                    <div class="col-md-6 mb-3">
                        [cite_start]<label class="form-label fw-bold">Jumlah Anggota HADIR [cite: 124]</label>
                        <input type="number" name="total_hadir" class="form-control" required min="0" placeholder="Contoh: 8">
                    </div>
                </div>

                <div class="mb-3">
                    [cite_start]<label class="form-label fw-bold">Nama Anggota Tidak Hadir [cite: 126]</label>
                    <textarea name="absen_names" class="form-control" rows="2" placeholder="Tulis nama anggota yang izin/sakit/alpa..."></textarea>
                </div>

                <div class="mb-3">
                    [cite_start]<label class="form-label fw-bold">Materi / KKP yang Disampaikan <span class="text-danger">*</span> [cite: 128]</label>
                    <textarea name="materi" class="form-control" rows="4" required placeholder="Ringkasan materi yang dibahas..."></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i> Simpan Laporan</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>