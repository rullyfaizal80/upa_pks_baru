<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
    // 1. Siapkan Nilai Tilawah (Amalan 3) untuk Tampilan
    $dbNilaiTilawah = floatval($laporan['amalan_3']); // Nilai di DB (Juz)
    $tampilanNilai  = 0;

    if ($jenjang == 'Muda') {
        // Jika Muda, DB (Juz) dikali 20 untuk jadi Halaman
        $tampilanNilai = $dbNilaiTilawah * 20;
    } else {
        // Jika Pratama, tetap Juz
        $tampilanNilai = $dbNilaiTilawah;
    }

    // Tentukan mana yang checked (Radio Preset atau Custom)
    $radio3Checked = '';
    $custom3Value  = '';

    // Array opsi preset untuk logika pengecekan
    $opsiMuda    = [7, 14]; 
    $opsiPratama = [1, 2, 3, 3.5];
    
    $opsiCek = ($jenjang == 'Muda') ? $opsiMuda : $opsiPratama;

    if (in_array($tampilanNilai, $opsiCek)) {
        $radio3Checked = $tampilanNilai; // Check radio sesuai angka
    } else {
        $radio3Checked = 'custom'; // Check radio custom
        $custom3Value  = $tampilanNilai; // Isi text box
    }

    // 2. Siapkan Nilai Puasa (Amalan 4)
    $nilaiPuasa = intval($laporan['amalan_4']);
    $radio4Checked = '';
    $manual4Value  = '';

    if ($nilaiPuasa <= 3) {
        $radio4Checked = $nilaiPuasa;
    } else {
        $radio4Checked = 'manual';
        $manual4Value  = $nilaiPuasa;
    }
?>

<div class="container-fluid">
    <div class="mb-3 border rounded p-3">
        <h4 class="mb-3 text-center">Edit Laporan (<?= $jenjang ?>)</h4>
        <div class="alert alert-secondary text-center py-2 mb-0">
            <strong>Periode Laporan:</strong><br>
            <?= date('d M Y', strtotime($laporan['periode_mulai'])) ?> s/d <?= date('d M Y', strtotime($laporan['periode_selesai'])) ?>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form action="/laporan/update/<?= $laporan['id'] ?>" method="post">
        <?= csrf_field() ?>

        <div class="mb-3 border rounded p-3">
            <label class="form-label fw-bold">Tanggal Pelaksanaan UPA</label>
            <input type="text" class="form-control bg-light" value="<?= formatTanggalIndo($laporan['tanggal_upa']) ?>" readonly>
            <div class="form-text text-muted">Tanggal dan Periode tidak dapat diubah di menu Edit. Hapus dan buat baru jika salah tanggal.</div>
        </div>

        <hr>

        <div class="mb-3 border rounded p-3">
            <label class="fw-bold">1. Sholat Berjamaah di Mesjid pekan ini (Laki-laki) *</label>
            <input type="number" name="amalan_1" class="form-control" min="0" max="35" value="<?= $laporan['amalan_1'] ?>" required>
        </div>

        <div class="mb-3 border rounded p-3">
            <label class="fw-bold d-block mb-2">2. Sholat Malam Pekan ini *</label>
            <div class="d-flex flex-wrap gap-3">
                <?php for ($i = 0; $i <= 7; $i++): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="amalan_2" id="amalan_2_<?= $i ?>" value="<?= $i ?>" <?= $laporan['amalan_2'] == $i ? 'checked' : '' ?>>
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
                        <input class="form-check-input" type="radio" name="amalan_3_radio" value="7" onclick="toggleCustomInput('amalan_3', false)" <?= $radio3Checked == 7 ? 'checked' : '' ?>>
                        <label class="form-check-label">Kurang dari 14 Halaman</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="amalan_3_radio" value="14" onclick="toggleCustomInput('amalan_3', false)" <?= $radio3Checked == 14 ? 'checked' : '' ?>>
                        <label class="form-check-label text-success fw-bold">14 Halaman (Tercapai)</label>
                    </div>
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input mt-0" type="radio" name="amalan_3_radio" value="custom" onclick="toggleCustomInput('amalan_3', true)" <?= $radio3Checked == 'custom' ? 'checked' : '' ?>>
                        <label class="form-check-label ms-2 me-2">Lainnya (Halaman):</label>
                        <input type="number" name="amalan_3_custom" id="amalan_3_custom_input" class="form-control form-control-sm" style="max-width: 100px;" 
                               value="<?= $custom3Value ?>" <?= $radio3Checked == 'custom' ? '' : 'disabled' ?>>
                    </div>
                </div>

            <?php else: ?>
                <div class="alert alert-success py-2 small">Target Pratama: 3.5 Juz/pekan. (Satuan: JUZ)</div>
                <div class="d-flex flex-column gap-2">
                    <?php foreach([1, 2, 3] as $val): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="amalan_3_radio" value="<?= $val ?>" onclick="toggleCustomInput('amalan_3', false)" <?= (string)$radio3Checked == (string)$val ? 'checked' : '' ?>>
                            <label class="form-check-label"><?= $val ?> Juz</label>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="amalan_3_radio" value="3.5" onclick="toggleCustomInput('amalan_3', false)" <?= (string)$radio3Checked == '3.5' ? 'checked' : '' ?>>
                        <label class="form-check-label text-success fw-bold">3.5 Juz (Target)</label>
                    </div>

                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input mt-0" type="radio" name="amalan_3_radio" value="custom" onclick="toggleCustomInput('amalan_3', true)" <?= $radio3Checked == 'custom' ? 'checked' : '' ?>>
                        <label class="form-check-label ms-2 me-2">Lainnya (Juz):</label>
                        <input type="text" name="amalan_3_custom" id="amalan_3_custom_input" class="form-control form-control-sm" style="max-width: 100px;" 
                               value="<?= $custom3Value ?>" <?= $radio3Checked == 'custom' ? '' : 'disabled' ?>>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3 border rounded p-3">
            <label class="fw-bold d-block mb-2">4. Shaum Sunnah Pekan ini *</label>
            <div class="d-flex flex-column gap-2">
                <?php for ($i = 0; $i <= 3; $i++): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="amalan_4" value="<?= $i ?>" onclick="toggleCustomInput('amalan_4', false)" <?= $radio4Checked == $i ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= $i ?> Hari</label>
                    </div>
                <?php endfor; ?>
                
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input mt-0" type="radio" name="amalan_4" value="manual" onclick="toggleCustomInput('amalan_4', true)" <?= $radio4Checked == 'manual' ? 'checked' : '' ?>>
                    <label class="form-check-label ms-2 me-2">Lebih dari 3:</label>
                    <input type="number" name="amalan_4_manual" id="amalan_4_custom_input" class="form-control form-control-sm" style="max-width: 80px;" 
                           value="<?= $manual4Value ?>" <?= $radio4Checked == 'manual' ? '' : 'disabled' ?>>
                </div>
            </div>
        </div>

        <div class="mb-3 border rounded p-3">
            <label class="fw-bold">5. Al-Ma'tsurat Pekan ini *</label>
            <input type="number" name="amalan_5" class="form-control" min="0" max="14" value="<?= $laporan['amalan_5'] ?>" required>
        </div>

        <div class="mb-3 border rounded p-3">
            <label class="fw-bold d-block mb-2">6. Sholat Dhuha Pekan ini *</label>
            <div class="d-flex flex-wrap gap-3">
                <?php for ($i = 0; $i <= 7; $i++): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="amalan_6" id="amalan_6_<?= $i ?>" value="<?= $i ?>" <?= $laporan['amalan_6'] == $i ? 'checked' : '' ?>>
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
                            <input class="form-check-input" type="radio" name="amalan_<?= $n ?>" id="amalan_<?= $n ?>_<?= $i ?>" value="<?= $i ?>" <?= $laporan["amalan_$n"] == $i ? 'checked' : '' ?>>
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
            <button type="submit" class="btn btn-warning w-50 py-2 fw-bold">
                <i class="bi bi-pencil-square"></i> UPDATE LAPORAN
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
</script>

<?= $this->endSection() ?>