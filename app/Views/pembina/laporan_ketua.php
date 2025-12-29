<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<style>
    /* 1. CSS UNTUK TAMPILAN LAYAR (WEB VIEW) */
    
    /* Membuat Kolom Pertama Freeze (Sticky) */
    .table-container {
        overflow-x: auto;
        position: relative;
    }
    
    /* Pastikan header dan sel pertama 'lengket' di kiri */
    .table-sticky th:first-child,
    .table-sticky td:first-child {
        position: sticky;
        left: 0;
        background-color: #fff; /* Wajib ada background agar tulisan tidak tembus */
        z-index: 2; /* Agar berada di atas konten lain saat discroll */
        border-right: 2px solid #dee2e6; /* Batas visual freeze */
    }
    
    /* Header butuh z-index lebih tinggi */
    .table-sticky th:first-child {
        z-index: 3;
        background-color: #e9ecef !important; /* Warna header bootstrap */
    }

    /* 2. CSS KHUSUS UNTUK CETAK (PRINT / PDF) */
    @media print {
        /* Reset layout browser */
        @page {
            size: A4 portrait;
            margin: 0mm 10mm;
        }

        body {
            background: white !important;
            font-family: 'Times New Roman', serif;
            color: black !important;
            -webkit-print-color-adjust: exact;
        }

        /* Sembunyikan elemen navigasi */
        .no-print, nav, header, aside, .btn, .form-select, footer {
            display: none !important;
        }

        /* Paksa Container Full Width tanpa Padding */
        .container-fluid, .card, .card-body {
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            box-shadow: none !important;
            width: 100% !important;
        }

       .row {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }

    .col-md-3, .col-6 {
        width: 100% !important;
        max-width: 100% !important;
    }

        /* Kecilkan font dan padding di dalam kartu agar muat */
        .border.rounded {
            border: 1px solid #000 !important; /* Border hitam tegas */
            padding: 5px !important;
            background: none !important; /* Hapus background warna warni */
        }
        .fs-2 { font-size: 14pt !important; font-weight: bold; margin-bottom: 0; }
        .fs-4 { font-size: 12pt !important; font-weight: bold; }
        .text-muted { color: black !important; font-size: 9pt !important; }

        /* Tabel dipadatkan */
        table { font-size: 10pt !important; width: 100% !important; table-layout: fixed; }
        th, td { padding: 4px !important; border: 1px solid #000 !important; }
        
        /* Hapus efek sticky saat print (karena kertas tidak bisa discroll) */
        .table-sticky th:first-child,
        .table-sticky td:first-child {
            position: static !important;
            border-right: 1px solid #000 !important;
        }
        .table-container { overflow: visible !important; }

        /* Header Laporan */
        h4 { font-size: 16pt !important; margin-bottom: 5px !important; }
        h5 { font-size: 12pt !important; margin-bottom: 20px !important; }
        h6 { font-size: 11pt !important; margin-top: 15px !important; border-bottom: 1px solid black !important; }

        /* Tanda Tangan agar tidak terpotong */
        .break-inside-avoid {
            page-break-inside: avoid;
        }
         
         /* MATIKAN GUTTER */
    .keterangan-amalan .row {
        display: flex !important;
        flex-wrap: nowrap !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        --bs-gutter-x: 0 !important;
    }

    /* 2 KOLOM FIX 50% */
    .keterangan-amalan .col-6 {
        width: 50% !important;
        flex: 0 0 50% !important;
        max-width: 50% !important;
        padding-left: 5px !important;
        padding-right: 5px !important;
        box-sizing: border-box !important;
    }
    .ttd-table,
    .ttd-table td {
        border: none !important;
    }
    }

     .logo-img {
            max-height: 80px; /* Atur tinggi logo */
            margin-bottom: 15px;
     }
     
</style>

<div class="container-fluid mt-4">

    <div class="card bg-light border-0 shadow-sm mb-4 no-print">
        <div class="card-body py-3">
            
            <div class="text-center mb-3">
                <h3 class="fw-bold text-primary m-0">
                    <i class="bi bi-bar-chart-line me-2"></i>Laporan Statistik Bulanan
                </h3>
            </div>

            <form action="" method="get" class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                
                <select name="bulan" class="form-select w-auto border-primary shadow-sm">
                    <?php 
                    $listBulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    foreach($listBulan as $k => $v): ?>
                        <option value="<?= $k ?>" <?= $k == $filter_bulan ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="tahun" class="form-select w-auto border-primary shadow-sm">
                    <?php for($i=date('Y'); $i>=2023; $i--): ?>
                        <option value="<?= $i ?>" <?= $i == $filter_tahun ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>

                <button type="submit" class="btn btn-primary shadow-sm fw-bold">
                    <i class="bi bi-search me-1"></i> Tampilkan
                </button>
                
                <button type="button" onclick="window.print()" class="btn btn-danger shadow-sm fw-bold">
                    <i class="bi bi-printer-fill me-1"></i> PDF
                </button>

            </form>

        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            
            <div class="text-center mb-4">
                <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" class="logo-img">
                <h4 class="fw-bold mb-1 text-uppercase">DPC ARCAMANIK</h4>
                <h4 class="fw-bold mb-1 text-uppercase">Laporan Pembinaan Anggota</h4>
                <h5 class="text-muted">Periode: <?= $listBulan[$filter_bulan] ?> <?= $filter_tahun ?></h5>
            </div>

            <h6 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-people-fill no-print"></i> DATA SDM & ANGGOTA</h6>
            
            <div class="row text-center mb-4 gx-2 gy-2">
                <div class="col-6 col-md-3">
                    <div class="border rounded p-3 bg-light h-100 d-flex flex-column justify-content-center">
                        <div class="text-muted small text-uppercase">Total Kelompok</div>
                        <div class="fs-2 fw-bold text-primary"><?= $total_kelompok ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded p-3 bg-light h-100 d-flex flex-column justify-content-center">
                        <div class="text-muted small text-uppercase">Total Anggota</div>
                        <div class="fs-2 fw-bold text-success"><?= $total_anggota ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded p-3 h-100 d-flex flex-column justify-content-center">
                        <div class="text-muted small">Anggota Laki-Laki</div>
                        <div class="fs-4 fw-bold"><?= $anggota_l ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded p-3 h-100 d-flex flex-column justify-content-center">
                        <div class="text-muted small">Anggota Perempuan</div>
                        <div class="fs-4 fw-bold"><?= $anggota_p ?></div>
                    </div>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered table-sm text-center w-100">
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
            </div>

            <h6 class="fw-bold border-bottom pb-2 mb-3 mt-4"><i class="bi bi-bar-chart-fill no-print"></i> RATA-RATA PENCAPAIAN AMALAN</h6>

            <?php 
                function renderRow($label, $data) {
                    if (!$data) $data = array_fill_keys(['avg1','avg2','avg3','avg4','avg5','avg6','avg7','avg8','avg9'], 0);
                    echo "<tr>";
                    // Kolom sticky pertama
                    echo "<td class='text-start ps-3 fw-bold' style='min-width: 140px;'>$label</td>";
                    
                    $val1 = number_format($data['avg1'], 0);
                    echo "<td class='".($data['avg1'] > 0 ? '' : 'text-muted fst-italic')."'>".($data['avg1'] > 0 ? $val1 : '-')."</td>";
                    echo "<td>".number_format($data['avg2'], 0)."</td>"; 
                    echo "<td>".number_format(round($data['avg3']*2)/2, 1)."</td>"; 
                    echo "<td>".number_format($data['avg4'], 0)."</td>";
                    echo "<td>".number_format($data['avg5'], 0)."</td>";
                    echo "<td>".number_format($data['avg6'], 0)."</td>";
                    echo "<td>".number_format($data['avg7'], 0)."</td>";
                    echo "<td>".number_format($data['avg8'], 0)."</td>";
                    echo "<td>".number_format($data['avg9'], 0)."</td>";
                    echo "</tr>";
                }
            ?>

            <div class="table-container mb-2">
                <table class="table table-bordered table-striped text-center small table-sticky mb-0">
                    <thead class="table-success">
                        <tr>
                            <th class="text-start ps-3">Kategori</th>
                            <th width="8%">A1</th>
                            <th width="8%">A2</th>
                            <th width="8%">A3</th>
                            <th width="8%">A4</th>
                            <th width="8%">A5</th>
                            <th width="8%">A6</th>
                            <th width="8%">A7</th>
                            <th width="8%">A8</th>
                            <th width="8%">A9</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php renderRow("Semua Anggota", $stat_all); ?>
                        <?php renderRow("Anggota Muda", $stat_muda); ?>
                        <?php renderRow("Anggota Pratama", $stat_pratama); ?>
                    </tbody>
                </table>
            </div>
            <div class="small text-muted fst-italic border rounded p-2 mt-3 bg-light no-print-bg keterangan-amalan">
                <div class="fw-bold mb-1">Keterangan Kode Amalan:</div>
                <div class="row">
                    <div class="col-6">
                        <ul class="list-unstyled mb-0">
                            <li><strong>A1 :</strong> Sholat Berjamaah di Masjid</li>
                            <li><strong>A2 :</strong> Sholat Malam (Qiyamullail)</li>
                            <li><strong>A3 :</strong> Membaca Al-Quran (Juz)</li>
                            <li><strong>A4 :</strong> Shaum Sunnah</li>
                            <li><strong>A5 :</strong> Al-Ma'tsurat</li>
                        </ul>
                    </div>
                    
                    <div class="col-6">
                        <ul class="list-unstyled mb-0">
                            <li><strong>A6 :</strong> Sholat Dhuha</li>
                            <li><strong>A7 :</strong> Olahraga</li>
                            <li><strong>A8 :</strong> Membaca Istighfar</li>
                            <li><strong>A9 :</strong> Membaca Shalawat</li>
                        </ul>
                    </div>
                </div>
            </div>

            <table class="ttd-table" style="width:100%; border-collapse:collapse;">
    <tr>
        <td style="width:50%;"></td>
        <td style="width:10%;"></td>

        <!-- KOLOM KE-3 -->
        <td style="width:40%; text-align:center; vertical-align:top;">
            <div style="margin-top:40px; page-break-inside:avoid;">
                <p style="margin-bottom:60px;">
                    Mengetahui,<br>
                    <strong>Ketua</strong>
                </p>

                <p style="font-weight:bold; text-decoration:underline;">
                    _______________________
                </p>
            </div>
        </td>
    </tr>
</table>

            

        </div>
    </div>
</div>

<?= $this->endSection() ?>