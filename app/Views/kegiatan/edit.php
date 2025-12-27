<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Edit Laporan UPA</h4>
        <a href="/kegiatan" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-dark py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i> Form Edit Laporan</h6>
        </div>
        <div class="card-body p-3 p-md-4">
            
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger shadow-sm">
                    <i class="bi bi-exclamation-triangle me-2"></i> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="/kegiatan/update/<?= $laporan['id'] ?>" method="post">
                <?= csrf_field() ?>
                
                <input type="hidden" name="kelompok_id" value="<?= $laporan['kelompok_id'] ?>">

                <div class="bg-light p-3 rounded border mb-4">
                    <div class="row">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <small class="text-muted d-block">Kelompok</small>
                            <span class="fw-bold text-primary"><?= esc($kelompok['nama_kelompok']) ?></span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Pembina saat Laporan dibuat</small>
                            <span class="fw-bold text-dark"><?= esc($kelompok['nama_pembina']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Tanggal Pelaksanaan</label>
                    <input type="date" name="tanggal" id="inputTanggal" class="form-control form-control-lg" required 
                           value="<?= old('tanggal', $laporan['tanggal']) ?>" 
                           max="<?= date('Y-m-d') ?>">
                    <div class="mt-2">
                        <span id="infoHari" class="badge bg-info text-dark d-none"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Teknis Pelaksanaan</label>
                        <select name="teknis" class="form-select form-select-lg" required>
                            <option value="Offline" <?= $laporan['teknis_pelaksanaan'] == 'Offline' ? 'selected' : '' ?>>Offline (Tatap Muka)</option>
                            <option value="Online" <?= $laporan['teknis_pelaksanaan'] == 'Online' ? 'selected' : '' ?>>Online (Daring)</option>
                            <option value="Hybrid" <?= $laporan['teknis_pelaksanaan'] == 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Kehadiran Pembina</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="pembina_hadir" id="h1" value="1" <?= $laporan['is_pembina_hadir'] == 1 ? 'checked' : '' ?>>
                            <label class="btn btn-outline-success py-2" for="h1"><i class="bi bi-check-circle-fill me-1"></i> Hadir</label>
                            
                            <input type="radio" class="btn-check" name="pembina_hadir" id="h0" value="0" <?= $laporan['is_pembina_hadir'] == 0 ? 'checked' : '' ?>>
                            <label class="btn btn-outline-danger py-2" for="h0"><i class="bi bi-x-circle-fill me-1"></i> Tidak</label>
                        </div>
                    </div>
                </div>

                <div class="card bg-light border-0 mb-4">
                    <div class="card-header bg-secondary text-white fw-bold">
                        <i class="bi bi-people-fill me-2"></i> Edit Presensi
                    </div>
                    <div class="card-body">
                        <div class="row row-cols-1 row-cols-md-2 g-2" style="max-height: 200px; overflow-y: auto;">
                            <?php if(empty($listAnggota)): ?>
                                <div class="col text-muted fst-italic">Data anggota kosong.</div>
                            <?php else: ?>
                                <?php foreach($listAnggota as $anggota): ?>
                                    <?php 
                                        $isAbsen = in_array($anggota['nama'], $arrayAbsen);
                                    ?>
                                    <div class="col">
                                        <div class="form-check p-2 border rounded <?= $isAbsen ? 'bg-danger bg-opacity-10 border-danger' : 'bg-white' ?>">
                                            <input class="form-check-input ms-1 absen-checkbox" type="checkbox" 
                                                   value="<?= esc($anggota['nama']) ?>" 
                                                   id="absen_<?= $anggota['id'] ?>"
                                                   <?= $isAbsen ? 'checked' : '' ?>> 
                                            
                                            <label class="form-check-label ms-2 w-75 fw-bold text-secondary" for="absen_<?= $anggota['id'] ?>" style="cursor: pointer;">
                                                <?= esc($anggota['nama']) ?>
                                            </label>
                                            <span class="badge bg-danger float-end status-label <?= $isAbsen ? '' : 'd-none' ?>">Absen</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6 col-md-6 mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Total Anggota</label>
                        <input type="number" name="total_anggota" id="totalAnggota" class="form-control form-control-lg text-center bg-white" required min="1" 
                               value="<?= old('total_anggota', $laporan['total_anggota']) ?>">
                    </div>
                    <div class="col-6 col-md-6 mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Total Hadir</label>
                        <input type="number" name="total_hadir" id="totalHadir" class="form-control form-control-lg text-center border-success bg-white fw-bold text-success" required min="0" 
                               value="<?= old('total_hadir', $laporan['total_hadir']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Nama Anggota Tidak Hadir</label>
                    <textarea name="absen_names" id="absenNames" class="form-control bg-light" rows="2" readonly><?= old('absen_names', $laporan['nama_tidak_hadir']) ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Materi / KKP</label>
                    <textarea name="materi" class="form-control" rows="5" required><?= old('materi', $laporan['materi']) ?></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning btn-lg py-3 fw-bold shadow-sm text-dark">
                        <i class="bi bi-save me-2"></i> SIMPAN PERUBAHAN
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. TANGGAL
        const inputTanggal = document.getElementById('inputTanggal');
        const infoHari = document.getElementById('infoHari');
        function updateTanggalIndo() {
            const dateValue = new Date(inputTanggal.value);
            if (!isNaN(dateValue.getTime())) {
                const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                infoHari.textContent = dateValue.toLocaleDateString('id-ID', options);
                infoHari.classList.remove('d-none');
            } else { infoHari.classList.add('d-none'); }
        }
        updateTanggalIndo();
        inputTanggal.addEventListener('change', updateTanggalIndo);

        // 2. ABSENSI OTOMATIS
        const checkboxes = document.querySelectorAll('.absen-checkbox');
        const inputTotalAnggota = document.getElementById('totalAnggota');
        const inputTotalHadir = document.getElementById('totalHadir');
        const textAreaAbsen = document.getElementById('absenNames');

        function hitungKehadiran() {
            let totalAnggota = parseInt(inputTotalAnggota.value) || 0;
            let jumlahTidakHadir = 0;
            let namaTidakHadir = [];

            checkboxes.forEach(chk => {
                const badge = chk.parentElement.querySelector('.status-label');
                if (chk.checked) {
                    jumlahTidakHadir++;
                    namaTidakHadir.push(chk.value);
                    chk.parentElement.classList.remove('bg-white');
                    chk.parentElement.classList.add('bg-danger', 'bg-opacity-10', 'border-danger');
                    badge.classList.remove('d-none');
                } else {
                    chk.parentElement.classList.add('bg-white');
                    chk.parentElement.classList.remove('bg-danger', 'bg-opacity-10', 'border-danger');
                    badge.classList.add('d-none');
                }
            });

            let totalHadir = totalAnggota - jumlahTidakHadir;
            if (totalHadir < 0) totalHadir = 0;

            inputTotalHadir.value = totalHadir;
            textAreaAbsen.value = namaTidakHadir.join(", ");
        }

        checkboxes.forEach(chk => chk.addEventListener('change', hitungKehadiran));
        inputTotalAnggota.addEventListener('input', hitungKehadiran);
    });
</script>

<?= $this->endSection() ?>