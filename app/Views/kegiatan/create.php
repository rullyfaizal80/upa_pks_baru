<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Laporan Pelaksanaan UPA</h4>
        <a href="/kegiatan" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-pen me-2"></i> Form Laporan Mingguan</h6>
        </div>
        <div class="card-body p-3 p-md-4">
            
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger shadow-sm">
                    <i class="bi bi-exclamation-triangle me-2"></i> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="/kegiatan/store" method="post">
                <?= csrf_field() ?>

                <div class="d-md-none bg-light p-3 rounded border mb-4">
                    <div class="small text-muted mb-1">INFO KELOMPOK:</div>
                    <div class="fw-bold text-primary mb-1"><?= $kelompok['nama_kelompok'] ?></div>
                    <div class="small text-dark">Pembina: <?= $kelompok['nama_pembina'] ?></div>
                </div>

                <div class="d-none d-md-flex row mb-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border h-100">
                            <small class="text-muted text-uppercase fw-bold">Kelompok</small>
                            <div class="fw-bold text-primary"><?= $kelompok['nama_kelompok'] ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border h-100">
                            <small class="text-muted text-uppercase fw-bold">Pembina</small>
                            <div class="fw-bold text-dark"><?= $kelompok['nama_pembina'] ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded border h-100">
                            <small class="text-muted text-uppercase fw-bold">Sekertaris</small>
                            <div class="fw-bold text-dark"><?= $kelompok['nama_sekertaris'] ?? '-' ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                    
                    <input type="date" name="tanggal" id="inputTanggal" class="form-control form-control-lg" required 
                           value="<?= old('tanggal', date('Y-m-d')) ?>" 
                           max="<?= date('Y-m-d') ?>">
                           
                    <div class="mt-2 d-flex align-items-center">
                        <span id="infoHari" class="badge bg-info text-dark me-2 d-none"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Teknis Pelaksanaan</label>
                        <select name="teknis" class="form-select form-select-lg" required>
                            <option value="Offline">Offline (Tatap Muka)</option>
                            <option value="Online">Online (Daring)</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Kehadiran Pembina</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="pembina_hadir" id="h1" value="1" checked>
                            <label class="btn btn-outline-success py-2" for="h1"><i class="bi bi-check-circle-fill me-1"></i> Hadir</label>
                            <input type="radio" class="btn-check" name="pembina_hadir" id="h0" value="0">
                            <label class="btn btn-outline-danger py-2" for="h0"><i class="bi bi-x-circle-fill me-1"></i> Tidak</label>
                        </div>
                    </div>
                </div>

                <div class="card bg-light border-0 mb-4">
                    <div class="card-header bg-secondary text-white fw-bold">
                        <i class="bi bi-people-fill me-2"></i> Presensi Anggota
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">
                            <i class="bi bi-info-circle"></i> Silakan <strong>Ceklis nama yang TIDAK HADIR</strong>. Jumlah kehadiran akan terhitung otomatis.
                        </p>
                        
                        <div class="row row-cols-1 row-cols-md-2 g-2" style="max-height: 200px; overflow-y: auto;">
                            <?php if(empty($listAnggota)): ?>
                                <div class="col text-muted fst-italic">Belum ada data anggota di kelompok ini.</div>
                            <?php else: ?>
                                <?php foreach($listAnggota as $anggota): ?>
                                    <div class="col">
                                        <div class="form-check p-2 bg-white border rounded">
                                            <input class="form-check-input ms-1 absen-checkbox" type="checkbox" value="<?= esc($anggota['nama']) ?>" id="absen_<?= $anggota['id'] ?>">
                                            <label class="form-check-label ms-2 w-75 fw-bold text-secondary" for="absen_<?= $anggota['id'] ?>" style="cursor: pointer;">
                                                <?= esc($anggota['nama']) ?>
                                            </label>
                                            <span class="badge bg-danger float-end d-none status-label">Absen</span>
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
                        <input type="number" name="total_anggota" id="totalAnggota" class="form-control form-control-lg text-center bg-white" required min="1" value="<?= count($listAnggota) ?>">
                    </div>
                    <div class="col-6 col-md-6 mb-3">
                        <label class="form-label fw-bold small text-uppercase text-secondary">Total Hadir</label>
                        <input type="number" name="total_hadir" id="totalHadir" class="form-control form-control-lg text-center border-success bg-white fw-bold text-success" required min="0" value="<?= count($listAnggota) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Nama Anggota Tidak Hadir</label>
                    <textarea name="absen_names" id="absenNames" class="form-control bg-light" rows="2" placeholder="Otomatis terisi jika ada yang diceklis di atas..." readonly></textarea>
                    <div class="form-text">Kolom ini terisi otomatis, tapi bisa diedit manual jika perlu (hapus atribut readonly lewat inspect element jika darurat).</div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Materi / KKP <span class="text-danger">*</span></label>
                    <textarea name="materi" class="form-control" rows="5" required placeholder="Tulis ringkasan materi..."></textarea>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold shadow-sm">
                        <i class="bi bi-save me-2"></i> SIMPAN LAPORAN
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. LOGIC TANGGAL INDONESIA
        const inputTanggal = document.getElementById('inputTanggal');
        const infoHari = document.getElementById('infoHari');
        function updateTanggalIndo() {
            const dateValue = new Date(inputTanggal.value);
            if (!isNaN(dateValue.getTime())) {
                const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                infoHari.textContent = dateValue.toLocaleDateString('id-ID', options);
                infoHari.classList.remove('d-none');
            } else {
                infoHari.classList.add('d-none');
            }
        }
        updateTanggalIndo();
        inputTanggal.addEventListener('change', updateTanggalIndo);

        // 2. LOGIC OTOMATIS ABSENSI
        const checkboxes = document.querySelectorAll('.absen-checkbox');
        const inputTotalAnggota = document.getElementById('totalAnggota');
        const inputTotalHadir = document.getElementById('totalHadir');
        const textAreaAbsen = document.getElementById('absenNames');

        function hitungKehadiran() {
            // Ambil nilai Total Anggota (Bisa diedit manual oleh user, jadi kita ambil value-nya)
            let totalAnggota = parseInt(inputTotalAnggota.value) || 0;
            let jumlahTidakHadir = 0;
            let namaTidakHadir = [];

            checkboxes.forEach(chk => {
                // Label badge 'Absen'
                const badge = chk.parentElement.querySelector('.status-label');
                
                if (chk.checked) {
                    jumlahTidakHadir++;
                    namaTidakHadir.push(chk.value); // Ambil nama dari value checkbox
                    
                    // Efek Visual
                    chk.parentElement.classList.remove('bg-white');
                    chk.parentElement.classList.add('bg-danger', 'bg-opacity-10', 'border-danger');
                    badge.classList.remove('d-none');
                } else {
                    // Reset Visual
                    chk.parentElement.classList.add('bg-white');
                    chk.parentElement.classList.remove('bg-danger', 'bg-opacity-10', 'border-danger');
                    badge.classList.add('d-none');
                }
            });

            // Rumus: Hadir = Total - Yang Diceklis
            let totalHadir = totalAnggota - jumlahTidakHadir;
            if (totalHadir < 0) totalHadir = 0; // Jaga-jaga biar gak minus

            // Update Inputan
            inputTotalHadir.value = totalHadir;
            textAreaAbsen.value = namaTidakHadir.join(", "); // Pisahkan nama dengan koma
        }

        // Jalankan fungsi saat checkbox diklik
        checkboxes.forEach(chk => {
            chk.addEventListener('change', hitungKehadiran);
        });

        // Jalankan fungsi jika user mengubah 'Total Anggota' secara manual
        inputTotalAnggota.addEventListener('input', hitungKehadiran);
    });
</script>

<?= $this->endSection() ?>