<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<style>
    @media print {
        /* Sembunyikan elemen navigasi saat print */
        .no-print, nav, header, aside, .btn {
            display: none !important;
        }
        /* Layout cetak */
        .card { border: none !important; shadow: none !important; }
        .container-fluid { padding: 0 !important; }
        body { background: white !important; font-size: 12pt; }
        h3, h5 { color: black !important; }
        .bg-light { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
    }
</style>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3 class="fw-bold text-primary">Laporan Statistik Bulanan</h3>
        
        <form action="" method="get" class="d-flex gap-2">
            <select name="bulan" class="form-select">
                <?php 
                $listBulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                foreach($listBulan as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $k == $filter_bulan ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
            <select name="tahun" class="form-select">
                <?php for($i=date('Y'); $i>=2023; $i--): ?>
                    <option value="<?= $i ?>" <?= $i == $filter_tahun ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i></button>
            <button type="button" onclick="window.print()" class="btn btn-danger"><i class="bi bi-file-pdf"></i> PDF/Cetak</button>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            
            <div class="text-center mb-5">
                <h4 class="fw-bold mb-1">LAPORAN PEMBINAAN ANGGOTA</h4>
                <h5 class="text-muted">Periode: <?= $listBulan[$filter_bulan] ?> <?= $filter_tahun ?></h5>
            </div>

            <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-people-fill"></i> DATA SDM & ANGGOTA</h6>
            
            <div class="row text-center mb-4">
                <div class="col-md-3 mb-3">
                    <div class="border rounded p-3 bg-light">
                        <div class="text-muted small text-uppercase">Total Kelompok</div>
                        <div class="fs-2 fw-bold text-primary"><?= $total_kelompok ?></div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="border rounded p-3 bg-light">
                        <div class="text-muted small text-uppercase">Total Anggota</div>
                        <div class="fs-2 fw-bold text-success"><?= $total_anggota ?></div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="border rounded p-3">
                        <div class="text-muted small">Anggota Laki-Laki</div>
                        <div class="fs-4 fw-bold"><?= $anggota_l ?></div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="border rounded p-3">
                        <div class="text-muted small">Anggota Perempuan</div>
                        <div class="fs-4 fw-bold"><?= $anggota_p ?></div>
                    </div>
                </div>
            </div>

            <table class="table table-bordered table-sm text-center mb-5">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="align-middle">Kategori</th>
                        <th colspan="2">Jenjang</th>
                        <th rowspan="2" class="align-middle">Total</th>
                    </tr>
                    <tr>
                        <th>Muda</th>
                        <th>Pratama</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-start ps-3 fw-bold">Pembina</td>
                        <td><?= $pembina_muda ?></td>
                        <td><?= $pembina_pratama ?></td>
                        <td class="fw-bold"><?= $pembina_muda + $pembina_pratama ?></td>
                    </tr>
                    <tr>
                        <td class="text-start ps-3 fw-bold">Sekertaris</td>
                        <td><?= $sekertaris_muda ?></td>
                        <td><?= $sekertaris_pratama ?></td>
                        <td class="fw-bold"><?= $sekertaris_muda + $sekertaris_pratama ?></td>
                    </tr>
                    <tr>
                        <td class="text-start ps-3 fw-bold">Anggota</td>
                        <td><?= $anggota_muda ?></td>
                        <td><?= $anggota_pratama ?></td>
                        <td class="fw-bold"><?= $anggota_muda + $anggota_pratama ?></td>
                    </tr>
                </tbody>
            </table>

            <h6 class="fw-bold border-bottom pb-2 mb-3 mt-5"><i class="bi bi-bar-chart-fill"></i> RATA-RATA PENCAPAIAN AMALAN</h6>

            <?php 
                // Helper render baris tabel
                function renderRow($label, $data) {
                    if (!$data) $data = array_fill_keys(['avg1','avg2','avg3','avg4','avg5','avg6','avg7','avg8','avg9'], 0);
                    echo "<tr>";
                    echo "<td class='text-start ps-3 fw-bold'>$label</td>";
                    
                    // Logic Jamaah (Amalan 1)
                    $val1 = number_format($data['avg1'], 0);
                    echo "<td class='".($data['avg1'] > 0 ? '' : 'text-muted fst-italic')."'>".($data['avg1'] > 0 ? $val1 : '-')."</td>";
                    
                    echo "<td>".number_format($data['avg2'], 0)."</td>"; // Qiyam
                    echo "<td>".number_format(round($data['avg3']*2)/2, 1)."</td>"; // Tilawah
                    echo "<td>".number_format($data['avg4'], 0)."</td>";
                    echo "<td>".number_format($data['avg5'], 0)."</td>";
                    echo "<td>".number_format($data['avg6'], 0)."</td>";
                    echo "<td>".number_format($data['avg7'], 0)."</td>";
                    echo "<td>".number_format($data['avg8'], 0)."</td>";
                    echo "<td>".number_format($data['avg9'], 0)."</td>";
                    echo "</tr>";
                }
            ?>

            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center small">
                    <thead class="table-success">
                        <tr>
                            <th class="text-start ps-3">Kategori</th>
                            <th width="8%">Jam'*</th>
                            <th width="8%">Qiyam</th>
                            <th width="8%">Tilawah</th>
                            <th width="8%">Shaum</th>
                            <th width="8%">Mats</th>
                            <th width="8%">Dhuha</th>
                            <th width="8%">OR</th>
                            <th width="8%">Istig</th>
                            <th width="8%">Shala</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php renderRow("Semua Anggota", $stat_all); ?>
                        <?php renderRow("Anggota Muda", $stat_muda); ?>
                        <?php renderRow("Anggota Pratama", $stat_pratama); ?>
                    </tbody>
                </table>
            </div>
            <div class="small text-muted fst-italic mt-2">
                * Keterangan: Rata-rata Shalat Berjamaah (Jam') <strong>hanya menghitung anggota Laki-Laki</strong>. Anggota Perempuan tidak dimasukkan dalam pembagi.
            </div>

            <div class="row mt-5 pt-5 break-inside-avoid">
                <div class="col-4 offset-8 text-center">
                    <p class="mb-5">Diketahui Oleh,<br>Ketua</p>
                    <br><br>
                    <p class="fw-bold text-decoration-underline">_______________________</p>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>