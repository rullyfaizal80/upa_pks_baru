<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<style>
    .table-responsive {
        overflow-x: auto;
    }
    /* STICKY COLUMN: Dibuat lebih kecil & teks dipotong jika kepanjangan */
    .sticky-col {
        position: sticky;
        left: 0;
        background-color: #fff; 
        z-index: 10;
        border-right: 2px solid #dee2e6;
        width: 130px;      
        min-width: 130px; 
        max-width: 130px;
        white-space: nowrap; 
        overflow: hidden;    
        text-overflow: ellipsis; 
        font-size: 0.85rem;
    }
    /* Fix header z-index */
    th.sticky-col {
        z-index: 11; 
        background-color: #f8f9fa;
        vertical-align: middle;
    }
</style>

<div class="container-fluid mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="/pembina" class="text-decoration-none text-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            <h3 class="mt-2 fw-bold">Monitoring: <?= $kelompok['nama_kelompok'] ?></h3>
        </div>
    </div>

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-week me-2"></i>Status Laporan Mingguan</h5>
        </div>
        <div class="card-body">
            
            <form action="" method="get" class="row g-2 align-items-end mb-4 bg-light p-3 rounded">
                <div class="col-auto">
                    <label class="small fw-bold text-muted">Tanggal Pekan:</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $filter_tanggal ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i></button>
                </div>
                <div class="col ms-auto text-end">
                    <span class="badge bg-info text-dark">
                        <?= date('d M', strtotime($periode['mulai'])) ?> - <?= date('d M Y', strtotime($periode['selesai'])) ?>
                    </span>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="sticky-col">Nama Anggota</th>
                            <th>Jenjang</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rekap_mingguan as $r): ?>
                        <tr>
                            <td class="sticky-col fw-bold text-dark" title="<?= $r['anggota']['nama'] ?>">
                                <?= $r['anggota']['nama'] ?>
                            </td>
                            <td class="text-center small"><?= $r['anggota']['jenjang'] ?></td>
                            <td class="text-center">
                                <?php if($r['status'] == 'Sudah Lapor'): ?>
                                    <span class="badge bg-success">Sudah</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Belum</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($r['status'] == 'Sudah Lapor'): ?>
                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $r['anggota']['id'] ?>">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-light text-muted" disabled>-</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-top border-4 border-success">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-success"><i class="bi bi-bar-chart-line me-2"></i>Rata-Rata Bulanan</h5>
        </div>
        <div class="card-body">
            
            <form action="" method="get" class="row g-2 mb-4 bg-light p-3 rounded">
                <input type="hidden" name="tanggal" value="<?= $filter_tanggal ?>">
                <div class="col-auto">
                    <select name="bulan" class="form-select form-select-sm">
                        <?php 
                        $bulanIndo = [1=>'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        foreach($bulanIndo as $k => $v): 
                        ?>
                            <option value="<?= $k ?>" <?= $k == $filter_bulan ? 'selected' : '' ?>><?= $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="tahun" class="form-select form-select-sm">
                        <?php for($i = date('Y'); $i >= 2023; $i--): ?>
                            <option value="<?= $i ?>" <?= $i == $filter_tahun ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success btn-sm">Cek</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle small" style="font-size: 0.8rem;">
                    <thead class="table-success">
                        <tr>
                            <th class="sticky-col text-start">Nama</th>
                            <th width="8%" title="Sholat Jamaah">Jam'</th>
                            <th width="8%" title="Qiyamul Lail">Qiyam</th>
                            <th width="8%" title="Tilawah (Juz/Hal)">Tilawah</th>
                            <th width="8%" title="Shaum">Shaum</th>
                            <th width="8%" title="Matsurat">Mats</th>
                            <th width="8%" title="Dhuha">Dhuha</th>
                            <th width="8%" title="Olahraga">OR</th>
                            <th width="8%" title="Istighfar">Istig</th>
                            <th width="8%" title="Shalawat">Shala</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rekap_bulanan as $rb): ?>
                        <tr>
                            <td class="sticky-col text-start fw-bold bg-white" title="<?= $rb['anggota']['nama'] ?>">
                                <?= $rb['anggota']['nama'] ?>
                            </td>
                            <?php if($rb['stats']): ?>
                                <td><?= number_format($rb['stats']['avg1'], 0) ?></td>
                                
                                <td><?= number_format($rb['stats']['avg2'], 0) ?></td>
                                
                                <?php 
                                    $rawTilawah = $rb['stats']['avg3'];
                                    // Rumus: (Nilai * 2) -> dibulatkan -> dibagi 2
                                    $tilawah05 = round($rawTilawah * 2) / 2;
                                ?>
                                <td><?= number_format($tilawah05, 1) ?></td> <td><?= number_format($rb['stats']['avg4'], 0) ?></td>
                                
                                <td><?= number_format($rb['stats']['avg5'], 0) ?></td>
                                
                                <td><?= number_format($rb['stats']['avg6'], 0) ?></td>
                                
                                <td><?= number_format($rb['stats']['avg7'], 0) ?></td>
                                
                                <td><?= number_format($rb['stats']['avg8'], 0) ?></td>
                                
                                <td><?= number_format($rb['stats']['avg9'], 0) ?></td>
                            <?php else: ?>
                                <td colspan="9" class="text-muted fst-italic">-</td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php foreach($rekap_mingguan as $r): ?>
    <?php if($r['status'] == 'Sudah Lapor'): ?>
    <div class="modal fade" id="modalDetail<?= $r['anggota']['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title small">Detail: <?= $r['anggota']['nama'] ?></h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <table class="table table-sm table-striped small mb-0">
                        <tr><td class="ps-3">Sholat Jamaah</td><td class="fw-bold text-end pe-3"><?= $r['laporan']['amalan_1'] ?></td></tr>
                        <tr><td class="ps-3">Qiyamul Lail</td><td class="fw-bold text-end pe-3"><?= $r['laporan']['amalan_2'] ?></td></tr>
                        
                        <tr>
                            <td class="ps-3">Tilawah</td>
                            <td class="fw-bold text-end pe-3">
                                <?= $r['laporan']['amalan_3'] ?> 
                                <?= ($r['anggota']['jenjang'] == 'Muda') ? 'Hal' : 'Juz' ?>
                            </td>
                        </tr>

                        <tr><td class="ps-3">Shaum</td><td class="fw-bold text-end pe-3"><?= $r['laporan']['amalan_4'] ?></td></tr>
                        <tr><td class="ps-3">Matsurat</td><td class="fw-bold text-end pe-3"><?= $r['laporan']['amalan_5'] ?></td></tr>
                        <tr><td class="ps-3">Dhuha</td><td class="fw-bold text-end pe-3"><?= $r['laporan']['amalan_6'] ?></td></tr>
                        <tr><td class="ps-3">Olahraga</td><td class="fw-bold text-end pe-3"><?= $r['laporan']['amalan_7'] ?></td></tr>
                        <tr><td class="ps-3">Istighfar</td><td class="fw-bold text-end pe-3"><?= $r['laporan']['amalan_8'] ?></td></tr>
                        <tr><td class="ps-3">Shalawat</td><td class="fw-bold text-end pe-3"><?= $r['laporan']['amalan_9'] ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
<?php endforeach; ?>

<?= $this->endSection() ?>