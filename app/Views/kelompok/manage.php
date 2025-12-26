<?= $this->extend('layout/main') ?>
<?= $this->section('css') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    /* Sedikit perbaikan agar Select2 pas dengan Bootstrap 5 */
    .select2-container--bootstrap-5 .select2-selection {
        border-color: #dee2e6; 
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-4">
        
        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-body">
                <h5 class="card-title text-primary"><?= esc($kelompok['nama_kelompok']) ?></h5>
                <hr>
                <div class="mb-2">
                    <small class="text-muted d-block">Pembina:</small>
                    <strong><?= esc($kelompok['nama_pembina']) ?></strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Sekertaris:</small>
                    <strong><?= esc($kelompok['nama_sekertaris']) ?></strong>
                </div>
            </div>
            <div class="card-footer bg-white">
                <a href="<?= base_url('kelompok') ?>" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0"><i class="bi bi-person-plus"></i> Tambah Anggota</h6>
            </div>
            <div class="card-body">
                <?php if(session()->get('errors')): ?>
                    <div class="alert alert-danger small p-2">
                        <?= implode('<br>', session()->get('errors')) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('kelompok/add-member') ?>" method="post">
                    <input type="hidden" name="kelompok_id" value="<?= $kelompok['id'] ?>">
                    
                    <div class="mb-3">
    <label class="form-label small fw-bold">Pilih Anggota</label>
    
    <select name="user_id" id="pilihAnggota" class="form-select" required>
        <option value="">-- Pilih Nama Anggota --</option>
        <?php foreach($calonAnggota as $ca): ?>
            <option value="<?= $ca['id'] ?>"><?= $ca['nama'] ?> (<?= $ca['jenjang'] ?>)</option>
        <?php endforeach; ?>
    </select>
        
</div>

                    <button type="submit" class="btn btn-success w-100">Tambahkan ke Kelompok</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Daftar Anggota (Binaan)</h6>
                <span class="badge bg-secondary"><?= count($members) ?> Orang</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nama Lengkap</th>
                            <th>Jenjang</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($members)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada anggota binaan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($members as $m): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?= esc($m['nama']) ?></td>
                                <td><?= esc($m['jenjang']) ?></td>
                                <td><span class="badge bg-light text-dark border">Anggota</span></td>
                                <td>
                                    <a href="<?= base_url('kelompok/remove-member/' . $m['id_anggota']) ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Keluarkan anggota ini?')" >
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#pilihAnggota').select2({
            theme: 'bootstrap-5', // Agar gayanya menyatu dengan Bootstrap
            placeholder: 'Atau ketik disini nama anggota',
            allowClear: true,
            width: '100%' // Penting agar tidak mengecil
        });
    });
</script>

<?= $this->endSection() ?>