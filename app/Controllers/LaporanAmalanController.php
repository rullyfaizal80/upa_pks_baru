<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LaporanAmalanModel;
use App\Models\AnggotaKelompokModel; // Pastikan model ini ada/dibuat

class LaporanAmalanController extends BaseController
{
    protected $laporanModel;
    protected $db;

    public function __construct()
    {
        $this->laporanModel = new LaporanAmalanModel();
        // Load Model Anggota Kelompok
        $this->anggotaModel = new \App\Models\AnggotaKelompokModel(); 
        $this->db = \Config\Database::connect();
        
        // PENTING: Load helper tanggal. 
        // Pastikan file app/Helpers/tanggal_helper.php SUDAH ADA.
        helper(['tanggal', 'form']); 
    }

    // --- LOGIC CHECKER PRIVATE (DIPERBAIKI) ---
    private function getKelompokUser($userId)
    {
        // Cari data anggota
        $anggota = $this->anggotaModel->where('user_id', $userId)->first();
        
        // Kembalikan kelompok_id jika ada, false jika tidak
        return $anggota ? $anggota['kelompok_id'] : false;
    }
   
    private function cekDeadlineEdit($bulanLaporan, $tahunLaporan)
    {
        // Logic: Maksimal tgl 4 bulan berikutnya
        // Contoh Laporan Bulan: 10 (Oktober), Tahun 2023
        // Deadline: 4 November 2023.
        
        $currentDate = date('Y-m-d');
        $deadlineDate = date('Y-m-d', strtotime("$tahunLaporan-$bulanLaporan-01 +1 month +3 days")); 
        // +1 month geser ke bulan depan tgl 1, +3 days jadi tgl 4.

        if ($currentDate > $deadlineDate) {
            return false; // Sudah lewat deadline
        }
        return true; // Masih boleh edit
    }
    // -----------------------------
    // =========================================================================
    // HELPER: CEK DEADLINE (Strict Mode)
    // =========================================================================
    private function cekApakahTerlambat($periodeMulai)
    {
        $today = date('Y-m-d'); // Tanggal hari ini (realtime)

        // 1. Ambil Bulan & Tahun dari Periode yang dipilih user
        $bulanLaporan = date('m', strtotime($periodeMulai));
        $tahunLaporan = date('Y', strtotime($periodeMulai));

        // 2. Hitung Deadline: Tanggal 4 bulan berikutnya
        // Rumus: Tgl 1 bulan laporan + 1 bulan + 3 hari = Tgl 4 bulan depan
        $deadline = date('Y-m-d', strtotime("$tahunLaporan-$bulanLaporan-01 +1 month +3 days"));

        // 3. Bandingkan
        // Jika Hari Ini (Today) lebih besar dari Deadline -> TERLAMBAT (True)
        if ($today > $deadline) {
            return true;
        }

        return false;
    }
    // =========================================================================
    // HELPER: CEK MASA DEPAN (Anti Time Traveler)
    // =========================================================================
    private function cekPeriodeMasaDepan($periodeMulai)
    {
        $today = date('Y-m-d'); // Tanggal hari ini
        
        // Jika Tanggal Mulai Periode LEBIH BESAR dari Hari Ini
        // Contoh: Periode mulai tgl 8, Hari ini tgl 5. (8 > 5 = True/Masa Depan)
        if ($periodeMulai > $today) {
            return true;
        }
        
        return false;
    }

    public function dashboard()
    {
        $userId = session()->get('id'); // Sesuaikan dengan session login Anda
        
        // 1. Cek User Punya Kelompok gak?
        $kelompokId = $this->getKelompokUser($userId);
        if (!$kelompokId) {
            return view('laporan/blocked', ['pesan' => 'Anda belum terdaftar dalam kelompok manapun. Silakan hubungi admin/murobbi.']);
        }

        // Ambil Filter
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');

        $laporan = $this->laporanModel
            ->where('user_id', $userId)
            ->where('YEAR(periode_mulai)', $tahun)
            ->where('MONTH(periode_mulai)', $bulan)
            ->orderBy('periode_mulai', 'ASC')
            ->findAll();

        $data = [
            'tahun'   => $tahun,
            'bulan'   => $bulan,
            'laporan' => $laporan,
            'title'   => 'Dashboard Yaumiyah'
        ];

        return view('laporan/dashboard', $data);
    }

    // =========================================================================
    // 1. METHOD CREATE (Menampilkan Form)
    // =========================================================================
    public function create()
    {
        $userId = session()->get('id');
        
        // A. Validasi Kelompok
        $kelompokId = $this->getKelompokUser($userId);
        if (!$kelompokId) {
            return redirect()->to('/laporan/dashboard')->with('error', 'Anda tidak memiliki kelompok.');
        }

        // B. Ambil Data User untuk cek Jenjang (PENTING untuk Tampilan Form)
        // Kita butuh tahu dia 'Muda' atau bukan agar form soal no 3 menyesuaikan
        $userModel = new \App\Models\UserModel(); 
        $user = $userModel->find($userId);
        $jenjang = $user['jenjang'] ?? 'Anggota'; // Default jika kosong

        $data = [
            'title'   => 'Isi Yaumiyah',
            'jenjang' => $jenjang // Kirim data jenjang ke View
        ];

        return view('laporan/create', $data);
    }

    // =========================================================================
    // 2. METHOD STORE (Memproses & Menyimpan Data)
    // =========================================================================
    public function store()
    {
        $userId = session()->get('id');
        
        // 1. Validasi Kelompok
        $kelompokId = $this->getKelompokUser($userId);
        if (!$kelompokId) return redirect()->to('/laporan/dashboard');

        // 2. Hitung Periode
        $tanggalUpa = $this->request->getPost('tanggal_upa');
        $periode    = hitungPeriodeMingguan($tanggalUpa);
        
        // --- [BARU] VALIDASI MASA DEPAN ---
        if ($this->cekPeriodeMasaDepan($periode['mulai'])) {
            return redirect()->back()->withInput()->with('error', 
                'Anda tidak bisa mengisi laporan untuk pekan yang belum dimulai/masa depan.'
            );
        }
        // ----------------------------------

        // --- VALIDASI DEADLINE (YANG TADI) ---
        if ($this->cekApakahTerlambat($periode['mulai'])) {
             // ... kode yang tadi ...
             return redirect()->back()->withInput()->with('error', 'Laporan periode ini sudah ditutup (Lewat deadline).');
        }

        // 3. Cek Dobel Input
        // ... (Kode selanjutnya sama persis seperti sebelumnya) ...
        if ($this->laporanModel->sudahIsi($userId, $periode['mulai'])) {
            return redirect()->back()->withInput()->with('error', 'Laporan pekan ini sudah pernah diisi.');
        }

        // ... LANJUTKAN KE BAWAH SEPERTI BIASA ...
        // (Ambil Jenjang, Logic Amalan 3, Logic Amalan 4, Save)
        
        // Agar Anda tidak bingung copy-paste, saya tulis singkat lanjutannya:
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $jenjang = $user['jenjang'] ?? 'Anggota';

        $amalan3Radio  = $this->request->getPost('amalan_3_radio');
        $amalan3Custom = $this->request->getPost('amalan_3_custom');
        $rawNilai3     = ($amalan3Radio === 'custom') ? $amalan3Custom : $amalan3Radio;

        if ($rawNilai3 === '' || $rawNilai3 === null) {
            return redirect()->back()->withInput()->with('error', 'Amalan No.3 (Tilawah) wajib diisi');
        }

        $finalAmalan3 = 0;
        if ($jenjang == 'Muda') {
            $finalAmalan3 = floatval($rawNilai3) / 20;
        } else {
            $finalAmalan3 = floatval($rawNilai3);
        }

        $amalan4Radio  = $this->request->getPost('amalan_4');
        $amalan4Manual = $this->request->getPost('amalan_4_manual');
        $finalAmalan4  = ($amalan4Manual !== '' && $amalan4Manual !== null) ? $amalan4Manual : $amalan4Radio;

        $this->laporanModel->save([
            'user_id'         => $userId,
            'kelompok_id'     => $kelompokId,
            'tanggal_upa'     => $tanggalUpa,
            'periode_mulai'   => $periode['mulai'],
            'periode_selesai' => $periode['selesai'],
            'amalan_1' => (int)$this->request->getPost('amalan_1'),
            'amalan_2' => (int)$this->request->getPost('amalan_2'),
            'amalan_3' => $finalAmalan3, 
            'amalan_4' => (int)$finalAmalan4,
            'amalan_5' => (int)$this->request->getPost('amalan_5'),
            'amalan_6' => (int)$this->request->getPost('amalan_6'),
            'amalan_7' => (int)$this->request->getPost('amalan_7'),
            'amalan_8' => (int)$this->request->getPost('amalan_8'),
            'amalan_9' => (int)$this->request->getPost('amalan_9'),
        ]);

        return redirect()->to('/laporan/dashboard')->with('success', 'Laporan berhasil disimpan!');
    }

   public function edit($id)
    {
        $userId  = session()->get('id');
        
        // 1. Cari Laporannya
        $laporan = $this->laporanModel->find($id);

        // 2. Validasi: Apakah laporan ada & milik user yang login?
        if (!$laporan || $laporan['user_id'] != $userId) {
            return redirect()->to('/laporan/dashboard')->with('error', 'Laporan tidak ditemukan atau bukan milik Anda.');
        }
        
        // 3. [INI YANG KURANG] Ambil Data Jenjang User
        // Kita wajib tahu jenjang user untuk mengatur tampilan Edit (Halaman vs Juz)
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $jenjang = $user['jenjang'] ?? 'Anggota';

        // 4. Kirim Data ke View
        $data = [
            'laporan' => $laporan,
            'jenjang' => $jenjang, // <--- Variable ini wajib dikirim agar error hilang
            'title'   => 'Edit Laporan'
        ];

        return view('laporan/edit', $data);
    }

    // ==========================================================
    // UPDATE METHOD (Dengan Validasi & Logika Jenjang)
    // ==========================================================
    public function update($id)
    {
        $userId  = session()->get('id');
        $laporan = $this->laporanModel->find($id);

        // 1. Validasi Kepemilikan
        if (!$laporan || $laporan['user_id'] != $userId) {
            return redirect()->to('/laporan/dashboard')->with('error', 'Laporan tidak ditemukan atau bukan milik Anda.');
        }

        // 2. Validasi Deadline (Tidak boleh edit jika sudah lewat tgl 4 bulan berikutnya)
        // Kita cek berdasarkan periode_mulai yang ada di database
        if ($this->cekApakahTerlambat($laporan['periode_mulai'])) {
            return redirect()->back()->with('error', 'Laporan ini sudah dikunci (Lewat deadline) dan tidak bisa diedit lagi.');
        }

        // 3. Ambil Jenjang User (Untuk Logic Hitung)
        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        $jenjang = $user['jenjang'] ?? 'Anggota';

        // 4. LOGIC AMALAN 3 (TILAWAH) - SAMA DENGAN STORE
        $amalan3Radio  = $this->request->getPost('amalan_3_radio');
        $amalan3Custom = $this->request->getPost('amalan_3_custom');
        $rawNilai3     = ($amalan3Radio === 'custom') ? $amalan3Custom : $amalan3Radio;

        if ($rawNilai3 === '' || $rawNilai3 === null) {
            return redirect()->back()->withInput()->with('error', 'Amalan No.3 (Tilawah) wajib diisi');
        }

        $finalAmalan3 = 0;
        if ($jenjang == 'Muda') {
            // Konversi dari Halaman ke Juz (DB simpan Juz)
            $finalAmalan3 = floatval($rawNilai3) / 20;
        } else {
            // User Pratama input Juz, simpan Juz
            $finalAmalan3 = floatval($rawNilai3);
        }

        // 5. LOGIC AMALAN 4 (PUASA)
        $amalan4Radio  = $this->request->getPost('amalan_4');
        $amalan4Manual = $this->request->getPost('amalan_4_manual');
        $finalAmalan4  = ($amalan4Manual !== '' && $amalan4Manual !== null) ? $amalan4Manual : $amalan4Radio;

        // 6. UPDATE DATA
        // Catatan: Tanggal UPA & Periode biasanya tidak diubah di edit untuk menjaga konsistensi,
        // tapi amalan-amalannya yang diperbaiki.
        $this->laporanModel->update($id, [
            'amalan_1' => (int)$this->request->getPost('amalan_1'),
            'amalan_2' => (int)$this->request->getPost('amalan_2'),
            'amalan_3' => $finalAmalan3,
            'amalan_4' => (int)$finalAmalan4,
            'amalan_5' => (int)$this->request->getPost('amalan_5'),
            'amalan_6' => (int)$this->request->getPost('amalan_6'),
            'amalan_7' => (int)$this->request->getPost('amalan_7'),
            'amalan_8' => (int)$this->request->getPost('amalan_8'),
            'amalan_9' => (int)$this->request->getPost('amalan_9'),
        ]);

        return redirect()->to('/laporan/dashboard')->with('success', 'Perubahan berhasil disimpan!');
    }

    // ==========================================================
    // DELETE METHOD (Dengan Validasi Bulan Berjalan)
    // ==========================================================
    public function delete($id)
    {
        $userId = session()->get('id');
        $laporan = $this->laporanModel->find($id);

        // 1. Validasi Kepemilikan
        if (!$laporan || $laporan['user_id'] != $userId) {
            return redirect()->to('/laporan/dashboard')->with('error', 'Laporan tidak ditemukan.');
        }

        // 2. Validasi Bulan Berjalan
        // Ambil bulan dari periode laporan (format YYYY-MM)
        $bulanLaporan = date('Y-m', strtotime($laporan['periode_mulai']));
        $bulanIni     = date('Y-m');

        // Jika bulan laporan KURANG DARI bulan ini (Masa lalu), tolak.
        if ($bulanLaporan < $bulanIni) {
            return redirect()->to('/laporan/dashboard')->with('error', 'Gagal menghapus. Laporan bulan lalu sudah dikunci.');
        }

        // 3. Proses Hapus
        $this->laporanModel->delete($id);

        return redirect()->to('/laporan/dashboard')->with('success', 'Laporan berhasil dihapus.');
    }
}