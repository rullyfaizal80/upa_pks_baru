<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<style>
    .form-container {
        max-width: 800px; /* Lebar standar di Laptop */
        margin: 0 auto;   /* Posisi tengah */
    }
    
    @media (max-width: 768px) {
        .form-container {
            width: 95% !important; /* Lebar 95% di HP */
            padding: 0 !important;
        }
        .card-body-mobile {
            padding: 1rem !important;
        }
    }
</style>

<div class="container-fluid mt-3">
    
    <div class="form-container">
        
        <div class="mb-3 border rounded p-3 bg-white shadow-sm">
            <h4 class="mb-3 text-center">Laporan Amalan Harian</h4>
            <small class="text-muted">
                <ul class="mb-0 ps-3">
                    <li>Laporan periode <strong>Senin–Ahad</strong></li>
                    <li>Amalan ke-3 disesuaikan dengan target jenjang</li>
                </ul>
            </small>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <form action="/laporan/store" method="post">
            <?= csrf_field() ?>

           <div class="mb-3 border rounded p-3">
            <label class="form-label fw-bold">Tanggal Pelaksanaan UPA</label>
            
            <input type="date" 
                   name="tanggal_upa" 
                   id="tanggal_upa_input" 
                   class="form-control" 
                   value="<?= old('tanggal_upa') ?>" 
                   required
                   onchange="updateTanggalIndo()">
            
            <div id="tanggal_preview" class="form-text text-primary fw-bold mt-2">
                </div>
        </div>

            <div class="mb-3 border rounded p-3 bg-white shadow-sm">
                
                <div class="mb-4">
                    <label class="fw-bold d-block">1. Sholat Berjamaah di Mesjid (Laki-laki)</label>
                    <small class="text-muted d-block mb-2">Perempuan dikosongkan</small>
                    <input type="number" name="amalan_1" class="form-control" placeholder="0-35" min="0" max="35" value="<?= old('amalan_1') ?>">
                </div>

                <hr class="text-muted">

                <div class="mb-4">
                    <label class="fw-bold d-block">2. Sholat Malam Pekan ini *</label>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <?php for ($i = 0; $i <= 7; $i++): ?>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="amalan_2" id="amalan2_<?= $i ?>" value="<?= $i ?>" <?= old('amalan_2') == $i ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="amalan2_<?= $i ?>"><?= $i ?></label>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <hr class="text-muted">

                <div class="mb-4">
                    <label class="fw-bold d-block mb-2">3. Tilawah Al-Qur'an Pekan ini *</label>

                    <?php if ($jenjang == 'Muda'): ?>
                        <div class="alert alert-info py-2 small">
                            <i class="bi bi-info-circle"></i> Target Muda: 2 Halaman/hari (14 Halaman/pekan).
                            <br><strong>Input dalam satuan HALAMAN.</strong>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="amalan_3_radio" id="muda_target" value="14" onclick="toggleCustomInput(false)">
                                <label class="form-check-label" for="muda_target">
                                    14 Halaman (Tercapai)
                                </label>
                            </div>

                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input mt-0" type="radio" name="amalan_3_radio" id="muda_custom" value="custom" onclick="toggleCustomInput(true)">
                                <label class="form-check-label ms-2 me-2" for="muda_custom">Jumlah Lain (Halaman):</label>
                                <input type="number" name="amalan_3_custom" id="amalan_3_custom_input" class="form-control form-control-sm" style="max-width: 100px;" placeholder="Cth: 10" disabled>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="alert alert-success py-2 small">
                            <i class="bi bi-info-circle"></i> Target Pratama: 0.5 Juz/hari (3.5 Juz/pekan).
                            <br><strong>Input dalam satuan JUZ.</strong>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="amalan_3_radio" id="pratama_1" value="1" onclick="toggleCustomInput(false)">
                                <label class="form-check-label" for="pratama_1">1 Juz</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="amalan_3_radio" id="pratama_2" value="2" onclick="toggleCustomInput(false)">
                                <label class="form-check-label" for="pratama_2">2 Juz</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="amalan_3_radio" id="pratama_3" value="3" onclick="toggleCustomInput(false)">
                                <label class="form-check-label" for="pratama_3">3 Juz</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="amalan_3_radio" id="pratama_target" value="3.5" onclick="toggleCustomInput(false)">
                                <label class="form-check-label fw-bold text-success" for="pratama_target">3.5 Juz (Target)</label>
                            </div>

                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input mt-0" type="radio" name="amalan_3_radio" id="pratama_custom" value="custom" onclick="toggleCustomInput(true)">
                                <label class="form-check-label ms-2 me-2" for="pratama_custom">Jumlah Lain (Juz):</label>
                                <input type="text" name="amalan_3_custom" id="amalan_3_custom_input" class="form-control form-control-sm" style="max-width: 100px;" placeholder="Cth: 4.5" disabled>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <hr class="text-muted">

                <div class="mb-4">
                    <label class="fw-bold d-block">4. Shaum Sunnah Pekan ini *</label>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <?php for ($i = 0; $i <= 3; $i++): ?>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="amalan_4" id="puasa_<?= $i ?>" value="<?= $i ?>" required>
                                <label class="form-check-label" for="puasa_<?= $i ?>"><?= $i ?> hari</label>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <input type="number" name="amalan_4_manual" class="form-control mt-2" placeholder="Isi manual jika lebih dari 3" style="display:none;"> 
                </div>

                <hr class="text-muted">

                <div class="mb-4">
                    <label class="fw-bold">5. Al-Ma'tsurat Pekan ini *</label>
                    <small class="text-muted d-block mb-2">Target: 14x (2x sehari)</small>
                    <input type="number" name="amalan_5" class="form-control" min="0" max="14" required>
                </div>

                <hr class="text-muted">
                
                <div class="mb-4">
                    <label class="fw-bold">6. Sholat Dhuha Pekan ini *</label>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <?php for ($i = 0; $i <= 7; $i++): ?>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="amalan_6" id="dhuha_<?= $i ?>" value="<?= $i ?>" required>
                                <label class="form-check-label" for="dhuha_<?= $i ?>"><?= $i ?></label>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="fw-bold">7. Olahraga Pekan ini *</label>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <?php for ($i = 0; $i <= 7; $i++): ?>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="amalan_7" id="sport_<?= $i ?>" value="<?= $i ?>" required>
                                <label class="form-check-label" for="sport_<?= $i ?>"><?= $i ?></label>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="fw-bold">8. Istighfar Pekan ini *</label>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <?php for ($i = 0; $i <= 7; $i++): ?>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="amalan_8" id="istighfar_<?= $i ?>" value="<?= $i ?>" required>
                                <label class="form-check-label" for="istighfar_<?= $i ?>"><?= $i ?></label>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="fw-bold">9. Shalawat Pekan ini *</label>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <?php for ($i = 0; $i <= 7; $i++): ?>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="amalan_9" id="shalawat_<?= $i ?>" value="<?= $i ?>" required>
                                <label class="form-check-label" for="shalawat_<?= $i ?>"><?= $i ?></label>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

            </div>

            <div class="d-flex gap-2 mt-4">
            <a href="/laporan/dashboard" class="btn btn-secondary w-50 py-2 fw-bold">
                <i class="bi bi-arrow-left"></i> KEMBALI
            </a>
            
            <button type="submit" class="btn btn-primary w-50 py-2 fw-bold">
                <i class="bi bi-save"></i> SIMPAN LAPORAN
            </button>
        </div>
        </form>
    </div>
</div>

<script>
// Fungsi yang tadi (Toggle Input)
function toggleCustomInput(amalan, isCustom) {
    if (amalan === 'amalan_3') {
        const input = document.getElementById('amalan_3_custom_input');
        input.disabled = !isCustom;
        if (isCustom) input.focus();
        else input.value = ''; 
    } 
    else if (amalan === 'amalan_4') {
        const input = document.getElementById('amalan_4_custom_input');
        input.disabled = !isCustom;
        if (isCustom) input.focus();
        else input.value = '';
    }
}

// === TAMBAHAN BARU: FORMAT TANGGAL INDONESIA ===
function updateTanggalIndo() {
    const input = document.getElementById('tanggal_upa_input').value;
    const preview = document.getElementById('tanggal_preview');
    
    if (input) {
        const date = new Date(input);
        const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        // Format ke ID-ID (Indonesia)
        preview.innerHTML = '<i class="bi bi-calendar-check"></i> ' + date.toLocaleDateString('id-ID', options);
    } else {
        preview.innerHTML = '';
    }
}
</script>

<?= $this->endSection() ?>