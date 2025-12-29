<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
// Definisi Array Bulan di awal agar bisa dipakai di Judul & Dropdown
$bulanIndo = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>

<style>
    .table-responsive-freeze {
        max-height: 600px;
        overflow-y: auto;
        overflow-x: auto;
        border: 1px solid #dee2e6;
        position: relative;
    }
    .table-responsive-freeze thead th {
        position: sticky;
        top: 0;
        background-color: #212529; /* Warna table-dark */
        color: white;
        z-index: 2;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    }
</style>

<div class="container-fluid px-4">
    <div class="mt-4 mb-4">
    
    <div class="mb-2">
        <h3 class="fw-bold m-0">Monitoring Pelaksanaan UPA</h3>
    </div>

    <div class="mb-3">
        <p class="text-muted m-0">
            Data Laporan Periode: 
            <span class="fw-bold text-primary">
                <?= strtoupper($bulanIndo[(int)$bulan]) ?> <?= $tahun ?>
            </span>
        </p>
    </div>
    
    <div>
        <form method="get" id="filterForm" class="d-flex align-items-center gap-2">
            <span class="small fw-bold text-muted">Filter:</span>
            
            <div style="width: auto; min-width: 140px;">
                <select name="bulan" class="form-select form-select-sm fw-bold border-primary shadow-sm" onchange="document.getElementById('filterForm').submit()">
                    <?php foreach ($bulanIndo as $i => $b): ?>
                        <option value="<?= $i ?>" <?= $bulan == $i ? 'selected' : '' ?>><?= $b ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="width: auto; min-width: 100px;">
                <select name="tahun" class="form-select form-select-sm fw-bold border-primary shadow-sm" onchange="document.getElementById('filterForm').submit()">
                    <?php for ($y = date('Y'); $y >= 2023; $y--): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </form>
    </div>

</div>

    <hr class="mb-4">

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Total Kelompok</div>
                            <div class="display-6 fw-bold"><?= $totalKelompok ?></div>
                        </div>
                        <i class="bi bi-people-fill display-6 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Sudah Lapor</div>
                            <div class="display-6 fw-bold"><?= $sudahLapor ?></div>
                        </div>
                        <i class="bi bi-check-circle-fill display-6 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Belum Lapor</div>
                            <div class="display-6 fw-bold"><?= $belumLapor ?></div>
                        </div>
                        <i class="bi bi-exclamation-triangle-fill display-6 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-white-50">Kepatuhan</div>
                            <div class="display-6 fw-bold"><?= $persentase ?>%</div>
                        </div>
                        <i class="bi bi-graph-up-arrow display-6 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center py-3">
                    <span><i class="bi bi-table me-2"></i> Detail Kelompok - <?= $bulanIndo[(int)$bulan] ?> <?= $tahun ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive-freeze">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nama Kelompok</th>
                                    <th>Pembina</th>
                                    <th class="text-center">Jml Laporan</th>
                                    <th class="text-center">Rata2 Hadir</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="min-width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monitoring as $row): ?>
                                    <tr>
                                        <td class="fw-bold"><?= esc($row['nama_kelompok']) ?></td>
                                        <td><?= esc($row['nama_pembina']) ?></td>
                                        
                                        <td class="text-center">
                                            <?php if($row['jumlah_laporan'] >= 4): ?>
                                                <span class="badge bg-success rounded-pill"><?= $row['jumlah_laporan'] ?></span>
                                            <?php elseif($row['jumlah_laporan'] > 0): ?>
                                                <span class="badge bg-warning text-dark rounded-pill"><?= $row['jumlah_laporan'] ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-danger rounded-pill">0</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <?= $row['rata_rata_hadir'] ? round($row['rata_rata_hadir'], 1) : '-' ?>
                                        </td>

                                        <td class="text-center">
                                            <?php if ($row['jumlah_laporan'] > 0): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success" style="font-size: 0.75rem;">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger" style="font-size: 0.75rem;">Pasif</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary btn-detail" 
                                                    data-id="<?= $row['id'] ?>"
                                                    title="Lihat Detail Mingguan">
                                                <i class="bi bi-eye-fill"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="bi bi-journal-text me-2"></i> Detail Laporan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modalLoader" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Mengambil data laporan...</p>
                </div>

                <div id="modalContent" class="d-none">
                    <h6 class="fw-bold mb-1 text-primary" id="modalNamaKelompok"></h6>
                    <p class="text-muted small mb-3">
                        Periode: <span class="fw-bold text-dark"><?= $bulanIndo[(int)$bulan] ?> <?= $tahun ?></span>
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th width="20%">Tanggal</th>
                                    <th width="15%">Teknis</th>
                                    <th>Materi / Ringkasan</th>
                                    <th width="15%" class="text-center">Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody id="modalTableBody"></tbody>
                        </table>
                    </div>
                    <div id="emptyMessage" class="alert alert-warning d-none text-center">
                        <i class="bi bi-info-circle me-1"></i> Belum ada laporan masuk untuk bulan ini.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const detailModalEl = document.getElementById('detailModal');
        const detailModal = new bootstrap.Modal(detailModalEl);
        
        const modalLoader = document.getElementById('modalLoader');
        const modalContent = document.getElementById('modalContent');
        const modalTableBody = document.getElementById('modalTableBody');
        const modalNamaKelompok = document.getElementById('modalNamaKelompok');
        const emptyMessage = document.getElementById('emptyMessage');

        // Gunakan selector langsung ke value dropdown saat ini
        const selectTahun = document.querySelector('select[name="tahun"]');
        const selectBulan = document.querySelector('select[name="bulan"]');

        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                const kelompokId = this.getAttribute('data-id');
                const currentTahun = selectTahun.value;
                const currentBulan = selectBulan.value;
                
                // Reset Modal
                modalLoader.classList.remove('d-none');
                modalContent.classList.add('d-none');
                modalTableBody.innerHTML = '';
                detailModal.show();

                // Fetch Data
                fetch(`/statistik/detail/${kelompokId}?tahun=${currentTahun}&bulan=${currentBulan}`)
                    .then(response => response.json())
                    .then(data => {
                        modalNamaKelompok.textContent = data.nama_kelompok;

                        if (data.laporan.length > 0) {
                            emptyMessage.classList.add('d-none');
                            data.laporan.forEach(lap => {
                                const dateObj = new Date(lap.tanggal);
                                const dateStr = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                                
                                const row = `
                                    <tr>
                                        <td class="fw-bold">${dateStr}</td>
                                        <td><span class="badge bg-secondary">${lap.teknis_pelaksanaan}</span></td>
                                        <td class="small">${lap.materi}</td>
                                        <td class="text-center fw-bold text-primary">
                                            ${lap.total_hadir} <span class="text-muted fw-normal" style="font-size:0.8em">/ ${lap.total_anggota}</span>
                                        </td>
                                    </tr>
                                `;
                                modalTableBody.innerHTML += row;
                            });
                        } else {
                            emptyMessage.classList.remove('d-none');
                        }

                        modalLoader.classList.add('d-none');
                        modalContent.classList.remove('d-none');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        modalLoader.innerHTML = '<p class="text-danger">Gagal memuat data.</p>';
                    });
            });
        });
    });
</script>

<?= $this->endSection() ?>