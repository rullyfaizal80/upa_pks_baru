<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    
    <div class="mb-3 border rounded p-3">
        <h4 class="mb-3 text-center">Laporan Amalan Harian</h4>
        <div class="alert alert-secondary text-center py-2 mb-0">
            <strong>Info Pengisian:</strong><br>
            Laporan untuk periode Senin – Ahad. Pastikan mengisi dengan jujur.
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
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
            
            <div id="tanggal_preview" class="form-text text-primary fw-bold mt-2"></div>
        </div>

        <hr>

        <div class="mb-3 border rounded p-3">
            <label class="fw-bold">1. Sholat Berjamaah di Mesjid pekan ini (Laki-laki) *</label>
            <div class="form-text text-muted mb-2">Perempuan isi 0.</div>
            <input type="number" name="amalan_1" class="form-control" min="0" max="35" value="<?= old('amalan_1') ?>" placeholder="0-35">
        </div>

        <div class="mb-3 border rounded p-3">
            <label class="fw-bold d-block mb-2">2. Sholat Malam Pekan ini *</label>
            <div class="d-flex flex-wrap gap-3">
                <?php for ($i = 0; $i <= 7; $i++): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="amalan_2" id="amalan_2_<?= $i ?>" value="<?= $i ?>" <?= old('amalan_2') === (string)$i ? 'checked' : '' ?> required>
                        <label class="form-check-label" for="amalan_2_<?= $i ?>"><?= $i ?></label>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="mb-3 border rounded p-3">
            <label class="fw-bold d-block mb-2">3. Tilawah Al-Qur'an Pekan ini *</label>

            <?php if ($jenjang == 'Muda'): ?>
                <div class="alert alert-info py-2 small">Target Muda: 14 Halaman/pekan. (Satuan: Halaman)</div>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="amalan_3_radio" value="14" onclick="toggleCustomInput('amalan_3', false)">
                        <label class="form-check-label text-success fw-bold">14 Halaman (Tercapai)</label>
                    </div>
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input mt-0" type="radio" name="amalan_3_radio" value="custom" onclick="toggleCustomInput('amalan_3', true)">
                        <label class="form-check-label ms-2 me-2">Lainnya (Halaman):</label>
                        <input type="number" name="amalan_3_custom" id="amalan_3_custom_input" class="form-control form-control-sm" style="max-width: 100px;" disabled>
                    </div>
                </div>

            <?php else: ?>
                <div class="alert alert-success py-2 small">Target Pratama: 3.5 Juz/pekan. (Satuan: JUZ)</div>
                <div class="d-flex flex-column gap-2">
                    <?php foreach([1, 2, 3] as $val): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="amalan_3_radio" value="<?= $val ?>" onclick="toggleCustomInput('amalan_3', false)">
                            <label class="form-check-label"><?= $val ?> Juz</label>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="amalan_3_radio" value="3.5" onclick="toggleCustomInput('amalan_3', false)">
                        <label class="form-check-label text-success fw-bold">3.5 Juz (Target)</label>
                    </div>

                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input mt-0" type="radio" name="amalan_3_radio" value="custom" onclick="toggleCustomInput('amalan_3', true)">
                        <label class="form-check-label ms-2 me-2">Lainnya (Juz):</label>
                        <input type="text" name="amalan_3_custom" id="amalan_3_custom_input" class="form-control form-control-sm" style="max-width: 100px;" disabled>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3 border rounded p-3">
            <label class="fw-bold d-block mb-2">4. Shaum Sunnah Pekan ini *</label>
            <div class="d-flex flex-column gap-2">
                <?php for ($i = 0; $i <= 3; $i++): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="amalan_4" value="<?= $i ?>" onclick="toggleCustomInput('amalan_4', false)">
                        <label class="form-check-label"><?= $i ?> Hari</label>
                    </div>
                <?php endfor; ?>
                
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input mt-0" type="radio" name="amalan_4" value="manual" onclick="toggleCustomInput('amalan_4', true)">
                    <label class="form-check-label ms-2 me-2">Lebih dari 3:</label>
                    <input type="number" name="amalan_4_manual" id="amalan_4_custom_input" class="form-control form-control-sm" style="max-width: 80px;" disabled>
                </div>
            </div>
        </div>

        <div class="mb-3 border rounded p-3">
            <label class="fw-bold">5. Al-Ma'tsurat Pekan ini *</label>
            <input type="number" name="amalan_5" class="form-control" min="0" max="14" value="<?= old('amalan_5') ?>" required>
        </div>

        <div class="mb-3 border rounded p-3">
            <label class="fw-bold d-block mb-2">6. Sholat Dhuha Pekan ini *</label>
            <div class="d-flex flex-wrap gap-3">
                <?php for ($i = 0; $i <= 7; $i++): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="amalan_6" id="amalan_6_<?= $i ?>" value="<?= $i ?>" <?= old('amalan_6') === (string)$i ? 'checked' : '' ?> required>
                        <label class="form-check-label" for="amalan_6_<?= $i ?>"><?= $i ?></label>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <?php 
        $labels = [
            7 => '7. Olahraga Pekan ini (Min 20 menit/hari) *',
            8 => '8. Membaca Istighfar Pekan ini (100x/hari) *',
            9 => '9. Membaca Shalawat Pekan ini (100x/hari) *'
        ];
        foreach ($labels as $n => $label): 
        ?>
            <div class="mb-3 border rounded p-3">
                <label class="fw-bold d-block mb-2"><?= $label ?></label>
                <div class="d-flex flex-wrap gap-3">
                    <?php for ($i = 0; $i <= 7; $i++): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="amalan_<?= $n ?>" id="amalan_<?= $n ?>_<?= $i ?>" value="<?= $i ?>" <?= old("amalan_$n") === (string)$i ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="amalan_<?= $n ?>_<?= $i ?>"><?= $i ?></label>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endforeach; ?>

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

<script>
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

function updateTanggalIndo() {
    const input = document.getElementById('tanggal_upa_input').value;
    const preview = document.getElementById('tanggal_preview');
    if (input) {
        const date = new Date(input);
        const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        preview.innerHTML = '<i class="bi bi-calendar-check"></i> ' + date.toLocaleDateString('id-ID', options);
    } else {
        preview.innerHTML = '';
    }
}
</script>

<?= $this->endSection() ?>