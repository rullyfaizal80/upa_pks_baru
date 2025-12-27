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
            margin: 10mm 15mm;
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

        /* Atur Ulang Grid System Bootstrap agar muat 1 baris */
        .row {
            display: flex !important;
            flex-wrap: nowrap !important; /* Paksa jangan turun baris */
            gap: 10px;
        }

        /* Paksa kartu menjadi 4 kolom sejajar (masing-masing 25%) */
        .col-md-3, .col-6 {
            width: 25% !important;
            flex: 0 0 25% !important;
            max-width: 25% !important;
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
    }
</style>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3 class="fw-bold text-primary">Laporan Statistik Bulanan</h3>
        
        <form action="" method="get" class="d-flex gap-2 align-items-center">
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
            <button type="button" onclick="window.print()" class="btn btn-danger"><i class="bi bi-printer"></i> PDF/Cetak</button>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            
            <div class="text-center mb-4">
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
                            <th width="8%">Jam'</th>
                            <th width="8%">Qiyam</th>
                            <th width="8%">Tila</th>
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
            
            <div class="small text-muted fst-italic">
                * Ket: Rata-rata Shalat Berjamaah (Jam') <strong>khusus Laki-Laki</strong>.
            </div>

            <div class="row mt-4 pt-3 break-inside-avoid">
                <div class="col-5 offset-7 text-center">
                    <p class="mb-5">Diketahui Oleh,<br>Ketua</p>
                    <br>
                    <p class="fw-bold text-decoration-underline">_______________________</p>
                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>