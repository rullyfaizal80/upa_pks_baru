<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LaporanKegiatanModel;

class KegiatanController extends BaseController
{
    protected $laporanModel;
    protected $db;

    public function __construct()
    {
        $this->laporanModel = new LaporanKegiatanModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $userId = session()->get('id');
        
        // 1. AMBIL SEMUA KELOMPOK (Bukan cuma satu)
        // Helper ini sekarang mengembalikan array of arrays (banyak baris)
        $listKelompok = $this->getAllKelompokUser($userId); 

        if (empty($listKelompok)) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak terdaftar di kelompok manapun sebagai Pembina atau Sekertaris.');
        }

        // 2. AMBIL FILTER (Default: Tahun & Bulan Ini)
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');

        // 3. SIAPKAN DATA PER KELOMPOK (Multi-Group Support)
        // Kita akan meloop setiap kelompok untuk mencari laporannya masing-masing
        $dataPerKelompok = [];

        foreach ($listKelompok as $grp) {
            // Query laporan murni berdasarkan kelompok_id
            $laporan = $this->laporanModel
                ->where('kelompok_id', $grp['id']) 
                ->where('YEAR(tanggal)', $tahun)
                ->where('MONTH(tanggal)', $bulan)
                ->orderBy('tanggal', 'DESC')
                ->findAll();

            $dataPerKelompok[] = [
                'info'    => $grp,      // Data Kelompok
                'laporan' => $laporan   // Data Laporan kelompok tersebut
            ];
        }

        $data = [
            'title'           => 'Laporan UPA',
            'dataPerKelompok' => $dataPerKelompok, // Dikirim sebagai array multi dimensi
            'tahun'           => $tahun,
            'bulan'           => $bulan
        ];

        return view('kegiatan/index', $data);
    }

    public function create()
    {
        $userId = session()->get('id');
        
        // REVISI: Tangkap ID Kelompok dari URL (dikirim dari tombol di halaman index)
        // Contoh URL: /kegiatan/create?kelompok_id=5
        $kelompokId = $this->request->getGet('kelompok_id');

        // Validasi: Cek apakah user berhak atas kelompok ID tersebut
        $kelompok = $this->getSpecificKelompok($userId, $kelompokId);

        if (!$kelompok) {
            return redirect()->to('/kegiatan')->with('error', 'Silakan pilih kelompok terlebih dahulu.');
        }

        // Ambil daftar anggota berdasarkan kelompok yang dipilih
        $listAnggota = $this->db->table('anggota_kelompok')
            ->select('users.id, users.nama')
            ->join('users', 'users.id = anggota_kelompok.user_id')
            ->where('kelompok_id', $kelompok['id'])
            ->orderBy('users.nama', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title'       => 'Buat Laporan Baru',
            'kelompok'    => $kelompok,
            'listAnggota' => $listAnggota
        ];

        return view('kegiatan/create', $data);
    }

    public function store()
    {
        $userId = session()->get('id');

        // 1. TANGKAP ID KELOMPOK DARI FORM (Input Hidden)
        $kelompokId = $this->request->getPost('kelompok_id');
        
        // 2. VALIDASI AKSES
        // Pastikan user adalah pengurus di kelompok ID tersebut
        $kelompok = $this->getSpecificKelompok($userId, $kelompokId);
        
        if (!$kelompok) {
            return redirect()->to('/kegiatan')->with('error', 'Gagal menyimpan. Data kelompok tidak valid atau akses ditolak.');
        }

        $tanggal = $this->request->getPost('tanggal');

        // 3. VALIDASI LOGIKA BISNIS
        if ($this->cekMasaDepan($tanggal)) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Tanggal pelaksanaan tidak boleh di masa depan.');
        }

        if ($this->cekApakahTerlambat($tanggal)) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Laporan bulan tersebut sudah ditutup (Deadline tgl 4).');
        }

        $cek = $this->laporanModel->cekLaporanMingguIni($kelompok['id'], $tanggal);
        if ($cek) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Laporan pekan tersebut sudah dibuat sebelumnya.');
        }

        // 4. SIMPAN DATA
        $this->laporanModel->save([
            'kelompok_id'        => $kelompok['id'], // Gunakan ID dari validasi
            'user_id'            => $userId,
            'pembina_id'         => $kelompok['pembina_id'], 
            'sekertaris_id'      => $kelompok['sekertaris_id'],
            'tanggal'            => $tanggal,
            'teknis_pelaksanaan' => $this->request->getPost('teknis'),
            'is_pembina_hadir'   => $this->request->getPost('pembina_hadir'),
            'total_anggota'      => $this->request->getPost('total_anggota'),
            'total_hadir'        => $this->request->getPost('total_hadir'),
            'nama_tidak_hadir'   => $this->request->getPost('absen_names'),
            'materi'             => $this->request->getPost('materi'),
        ]);

        return redirect()->to('/kegiatan')->with('success', 'Alhamdulillah, Laporan UPA berhasil disimpan.');
    }

    public function edit($id)
    {
        $userId = session()->get('id');
        
        // 1. Ambil Laporan
        $laporan = $this->laporanModel->find($id);

        if (!$laporan) {
            return redirect()->to('/kegiatan')->with('error', 'Laporan tidak ditemukan.');
        }

        // 2. REVISI: Validasi Kelompok Berdasarkan ID Kelompok di Laporan
        // Kita harus memastikan user yang login adalah pengurus dari kelompok pemilik laporan ini
        $kelompok = $this->getSpecificKelompok($userId, $laporan['kelompok_id']);

        if (!$kelompok) {
             return redirect()->to('/kegiatan')->with('error', 'Anda tidak memiliki akses ke laporan ini.');
        }

        // 3. Cek Deadline (Kunci)
        if ($this->cekApakahTerlambat($laporan['tanggal'])) {
            return redirect()->to('/kegiatan')->with('error', 'Maaf, laporan ini sudah terkunci (Arsip).');
        }

        // 4. Ambil Anggota Kelompok Terkait
        $listAnggota = $this->db->table('anggota_kelompok')
            ->select('users.id, users.nama')
            ->join('users', 'users.id = anggota_kelompok.user_id')
            ->where('kelompok_id', $laporan['kelompok_id'])
            ->orderBy('users.nama', 'ASC')
            ->get()->getResultArray();

        // 5. Parsing Nama Absen
        $arrayAbsen = [];
        if (!empty($laporan['nama_tidak_hadir'])) {
            $arrayAbsen = array_map('trim', explode(',', $laporan['nama_tidak_hadir']));
        }

        $data = [
            'title'       => 'Edit Laporan UPA',
            'kelompok'    => $kelompok,
            'laporan'     => $laporan,
            'listAnggota' => $listAnggota,
            'arrayAbsen'  => $arrayAbsen
        ];

        return view('kegiatan/edit', $data);
    }

    public function update($id)
    {
        $userId = session()->get('id');
        $laporanLama = $this->laporanModel->find($id);

        if (!$laporanLama) {
            return redirect()->to('/kegiatan')->with('error', 'Laporan tidak ditemukan.');
        }

        // =====================================================================
        // PERBAIKAN VALIDASI KELOMPOK
        // =====================================================================
        // Ambil ID Kelompok dari data laporan lama (Lebih aman dari manipulasi form)
        $targetKelompokId = $laporanLama['kelompok_id'];
        
        // Cek apakah user berhak atas kelompok ini
        $kelompok = $this->getSpecificKelompok($userId, $targetKelompokId);
        
        if (!$kelompok) {
             return redirect()->to('/kegiatan')->with('error', 'Data kelompok tidak valid atau Anda tidak memiliki akses.');
        }

        // 1. Cek Deadline
        if ($this->cekApakahTerlambat($laporanLama['tanggal'])) {
            return redirect()->to('/kegiatan')->with('error', 'Laporan sudah terkunci (Arsip).');
        }

        $tanggalBaru = $this->request->getPost('tanggal');

        // 2. Validasi Tanggal Masa Depan
        if ($this->cekMasaDepan($tanggalBaru)) {
            return redirect()->back()->withInput()->with('error', 'Tanggal tidak boleh di masa depan.');
        }

        // 3. Simpan Perubahan
        $this->laporanModel->update($id, [
            'tanggal'            => $tanggalBaru,
            'teknis_pelaksanaan' => $this->request->getPost('teknis'),
            'is_pembina_hadir'   => $this->request->getPost('pembina_hadir'),
            'total_anggota'      => $this->request->getPost('total_anggota'),
            'total_hadir'        => $this->request->getPost('total_hadir'),
            'nama_tidak_hadir'   => $this->request->getPost('absen_names'),
            'materi'             => $this->request->getPost('materi'),
        ]);

        return redirect()->to('/kegiatan')->with('success', 'Perubahan laporan berhasil disimpan.');
    }

    public function delete($id)
    {
        $userId = session()->get('id');
        $laporan = $this->laporanModel->find($id);

        if (!$laporan) return redirect()->to('/kegiatan');

        // Validasi Akses
        $kelompok = $this->getSpecificKelompok($userId, $laporan['kelompok_id']);
        if (!$kelompok) return redirect()->to('/kegiatan')->with('error', 'Akses ditolak.');

        // Cek Deadline
        if ($this->cekApakahTerlambat($laporan['tanggal'])) {
            return redirect()->to('/kegiatan')->with('error', 'Gagal menghapus. Laporan terkunci.');
        }

        $this->laporanModel->delete($id);

        return redirect()->to('/kegiatan')->with('success', 'Laporan berhasil dihapus.');
    }

    // =========================================================================
    // PRIVATE HELPER (REVISI LOGIC MULTI-GROUP)
    // =========================================================================

    // Helper 1: Mengambil SEMUA kelompok dimana user terlibat (untuk Index)
    private function getAllKelompokUser($userId)
    {
        return $this->db->table('kelompok')
            ->select('kelompok.*, p.nama as nama_pembina, s.nama as nama_sekertaris')
            ->join('users p', 'p.id = kelompok.pembina_id')
            ->join('users s', 's.id = kelompok.sekertaris_id', 'left')
            ->groupStart()
                ->where('kelompok.pembina_id', $userId)
                ->orWhere('kelompok.sekertaris_id', $userId)
            ->groupEnd()
            ->orderBy('kelompok.nama_kelompok', 'ASC')
            ->get()->getResultArray(); // PENTING: getResultArray (Banyak Baris)
    }

    // Helper 2: Validasi & Ambil SATU kelompok spesifik (untuk Create/Edit)
    private function getSpecificKelompok($userId, $kelompokId)
    {
        if(empty($kelompokId)) return false;

        return $this->db->table('kelompok')
            ->select('kelompok.*, p.nama as nama_pembina, s.nama as nama_sekertaris')
            ->join('users p', 'p.id = kelompok.pembina_id')
            ->join('users s', 's.id = kelompok.sekertaris_id', 'left')
            ->groupStart()
                ->where('kelompok.pembina_id', $userId)
                ->orWhere('kelompok.sekertaris_id', $userId)
            ->groupEnd()
            ->where('kelompok.id', $kelompokId) // FILTER KHUSUS ID KELOMPOK
            ->get()->getRowArray();
    }

    private function cekMasaDepan($tanggalInput)
    {
        return $tanggalInput > date('Y-m-d');
    }

    private function cekApakahTerlambat($tanggalInput)
    {
        $today = date('Y-m-d');
        $bulanLaporan = date('m', strtotime($tanggalInput));
        $tahunLaporan = date('Y', strtotime($tanggalInput));
        $deadline = date('Y-m-d', strtotime("$tahunLaporan-$bulanLaporan-01 +1 month +3 days"));
        return $today > $deadline;
    }
}