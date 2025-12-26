<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid mt-4">

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-0 fw-bold"><?= $kelompok['nama_kelompok'] ?></h4>
                    <p class="text-muted mb-0">
                        Periode: <strong><?= date('d M Y', strtotime($periode['mulai'])) ?></strong> s/d <strong><?= date('d M Y', strtotime($periode['selesai'])) ?></strong>
                    </p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <form action="" method="get" class="d-flex justify-content-md-end gap-2">
                        <input type="date" name="tanggal" class="form-control w-auto" value="<?= $filter_tanggal ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter"></i> Cek Periode
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-people"></i> Status Laporan Anggota</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama Anggota</th>
                            <th class="text-center">Jenjang</th>
                            <th class="text-center">Status Laporan</th>
                            <th class="text-center">Tilawah</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($rekap_data)): ?>
                            <tr><td colspan="6" class="text-center py-4">Belum ada anggota di kelompok ini.</td></tr>
                        <?php else: ?>
                            <?php $no=1; foreach($rekap_data as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                
                                <td>
                                    <div class="fw-bold"><?= $row['anggota']['nama'] ?></div>
                                    <small class="text-muted">@<?= $row['anggota']['username'] ?></small>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $row['anggota']['jenjang'] ?></span>
                                </td>

                                <td class="text-center">
                                    <?php if($row['status'] == 'Sudah Lapor'): ?>
                                        <span class="badge bg-success py-2 px-3 rounded-pill">
                                            <i class="bi bi-check-circle-fill"></i> SUDAH
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger py-2 px-3 rounded-pill">
                                            <i class="bi bi-x-circle-fill"></i> BELUM
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <?php if($row['laporan']): ?>
                                        <?php 
                                            // Tampilkan Tilawah sesuai Jenjang
                                            $nilai = floatval($row['laporan']['amalan_3']);
                                            if($row['anggota']['jenjang'] == 'Muda') {
                                                echo ($nilai * 20) . " Halaman";
                                            } else {
                                                echo $nilai . " Juz";
                                            }
                                        ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <?php if($row['laporan']): ?>
                                        <button class="btn btn-sm btn-info text-white" title="Lihat Detail">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    <?php else: ?>
                                        <a href="https://wa.me/?text=Assalamualaikum%20<?= $row['anggota']['nama'] ?>,%20mohon%20segera%20isi%20laporan%20yaumiyah%20pekan%20ini." target="_blank" class="btn btn-sm btn-success" title="Ingatkan via WA">
                                            <i class="bi bi-whatsapp"></i> Ingatkan
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="/pembina" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

</div>

<?= $this->endSection() ?>