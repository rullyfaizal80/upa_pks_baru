<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
function formatTanggalIndo($datetime) {
    $hariIndo = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
    $bulanIndo = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    $timestamp = strtotime($datetime);
    $hari      = $hariIndo[date('w', $timestamp)];
    $tgl       = date('d', $timestamp);
    $bulan     = $bulanIndo[(int)date('n', $timestamp)];
    $tahun     = date('Y', $timestamp);
    $jam       = date('H:i', $timestamp);

    return "$hari, $tgl $bulan $tahun <span class='badge bg-light text-dark border ms-1'>$jam WIB</span>";
}
?>

<div class="container-fluid px-0">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 fw-bold text-dark">Dashboard</h2>
            <p class="text-muted mb-0">
                Selamat Datang, <span class="fw-bold text-primary"><?= session()->get('nama') ?></span>!
            </p>
        </div>
        <div class="text-end d-none d-md-block">
            <small class="text-muted d-block mb-1">Role Anda:</small>
            <?php foreach(session()->get('roles') as $role): ?>
                <span class="badge bg-primary rounded-pill text-uppercase"><?= $role ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm border-0 border-start border-primary border-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold text-primary">
                        <i class="bi bi-megaphone-fill me-2"></i> Papan Pengumuman
                    </h5>
                    
                    <?php if (isset($can_manage_pengumuman) && $can_manage_pengumuman) : ?>
                        <button class="btn btn-sm btn-primary btn-tambah" data-bs-toggle="modal" data-bs-target="#modalPengumuman">
                            <i class="bi bi-plus-lg"></i> Buat Pengumuman
                        </button>
                    <?php endif; ?>
                </div>
                
                <div class="card-body p-0">
                    <?php if (!empty($pengumuman_list)) : ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($pengumuman_list as $info) : ?>
                                <div class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        
                                        <div class="flex-grow-1">
                                            <div class="d-flex flex-wrap w-100 justify-content-between align-items-center mb-2">
                                                <h5 class="mb-1 fw-bold text-dark"><?= esc($info['judul']) ?></h5>
                                                <small class="text-muted" style="font-size: 0.85rem;">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    <?= formatTanggalIndo($info['tanggal']) ?>
                                                </small>
                                            </div>                                          
                                            
                                            <p class="mb-0 text-secondary" style="font-size: 1rem; line-height: 1.6; white-space: pre-line;"><?= esc($info['isi']) ?></p>
                                        </div>

                                        <?php if (isset($can_manage_pengumuman) && $can_manage_pengumuman) : ?>
                                            <div class="d-flex flex-column ms-3 gap-2">
                                                <button class="btn btn-outline-warning btn-sm border-0 btn-edit" 
                                                        data-id="<?= $info['id'] ?>"
                                                        data-judul="<?= esc($info['judul']) ?>"
                                                        data-isi="<?= esc($info['isi']) ?>"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalPengumuman"
                                                        title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <a href="<?= base_url('dashboard/pengumuman/hapus/'.$info['id']) ?>" 
                                                   class="btn btn-outline-danger btn-sm border-0" 
                                                   onclick="return confirm('Yakin ingin menghapus pengumuman ini?')"
                                                   title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard-x display-4 mb-2 d-block opacity-25"></i>
                            <span class="small">Belum ada pengumuman terbaru.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                <div>
                    Silakan pilih menu di samping (atau tombol menu di pojok kanan atas pada HP) untuk mulai mengelola data laporan dan kelompok.
                </div>
            </div>
        </div>
    </div>

</div>

<?php if (isset($can_manage_pengumuman) && $can_manage_pengumuman) : ?>
<div class="modal fade" id="modalPengumuman" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"> 
        <form id="formPengumuman" action="<?= base_url('dashboard/pengumuman/tambah') ?>" method="post" class="w-100">
            
            <input type="hidden" name="id" id="pengumumanId">

            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title fw-bold" id="modalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Buat Pengumuman Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Judul Pengumuman</label>
                        <input type="text" name="judul" id="inputJudul" 
                               class="form-control form-control-lg border-0 shadow-sm" 
                               placeholder="Contoh: Undangan Rihlah Gabungan" 
                               style="font-weight: 600;"
                               required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark">Isi Pesan</label>
                        <textarea name="isi" id="inputIsi" 
                                  class="form-control border-0 shadow-sm p-3" 
                                  rows="10" 
                                  placeholder="Tulis detail pengumuman disini..." 
                                  style="font-size: 1rem; line-height: 1.5; resize: vertical;"
                                  required></textarea>
                        <div class="form-text mt-2 text-muted"><i class="bi bi-info-circle me-1"></i> Tekan <b>Enter</b> untuk membuat paragraf baru.</div>
                    </div>
                </div>

                <div class="modal-footer bg-white py-3">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                        <i class="bi bi-send me-2"></i> Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById('modalPengumuman');
        const form = document.getElementById('formPengumuman');
        const modalLabel = document.getElementById('modalLabel');
        const inputId = document.getElementById('pengumumanId');
        const inputJudul = document.getElementById('inputJudul');
        const inputIsi = document.getElementById('inputIsi');

        const urlTambah = "<?= base_url('dashboard/pengumuman/tambah') ?>";
        const urlUpdate = "<?= base_url('dashboard/pengumuman/update') ?>";

        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; 
            const isEdit = button.classList.contains('btn-edit');

            if (isEdit) {
                modalLabel.innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Pengumuman';
                form.action = urlUpdate;
                inputId.value = button.getAttribute('data-id');
                inputJudul.value = button.getAttribute('data-judul');
                inputIsi.value = button.getAttribute('data-isi');
            } else {
                modalLabel.innerHTML = '<i class="bi bi-plus-lg me-2"></i>Buat Pengumuman Baru';
                form.action = urlTambah;
                form.reset();
                inputId.value = '';
            }
        });
    });
</script>
<?php endif; ?>

<?= $this->endSection() ?>