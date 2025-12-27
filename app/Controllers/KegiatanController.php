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
        $kelompok = $this->getKelompokUser($userId); 

        if (!$kelompok) {
            return redirect()->to('/dashboard')->with('error', 'Anda bukan pengurus kelompok manapun.');
        }

        // 1. AMBIL FILTER (Default: Tahun & Bulan Ini)
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');

        // 2. QUERY DATA
        $laporan = $this->laporanModel
            ->where('kelompok_id', $kelompok['id'])
            ->where('YEAR(tanggal)', $tahun)
            ->where('MONTH(tanggal)', $bulan)
            ->orderBy('tanggal', 'DESC')
            ->findAll();

        $data = [
            'title'    => 'Laporan UPA',
            'kelompok' => $kelompok,
            'laporan'  => $laporan,
            'tahun'    => $tahun, // Untuk selected di dropdown
            'bulan'    => $bulan  // Untuk selected di dropdown
        ];

        return view('kegiatan/index', $data);
    }

    public function create()
    {
        $userId = session()->get('id');
        $kelompok = $this->getKelompokUser($userId);

        if (!$kelompok) return redirect()->back();

        // Ambil daftar anggota untuk fitur Checklist
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
        $kelompok = $this->getKelompokUser($userId);
        
        if (!$kelompok) return redirect()->back();

        $tanggal = $this->request->getPost('tanggal');

        // =====================================================================
        // VALIDASI 1: CEK MASA DEPAN (Anti Time Traveler)
        // =====================================================================
        if ($this->cekMasaDepan($tanggal)) {
            return redirect()->back()->withInput()->with('error', 
                'Gagal: Tanggal pelaksanaan tidak boleh di masa depan (belum terjadi).'
            );
        }

        // =====================================================================
        // VALIDASI 2: CEK DEADLINE (Logic Yaumiyah)
        // Batas input laporan bulan lalu adalah tanggal 4 bulan ini.
        // =====================================================================
        if ($this->cekApakahTerlambat($tanggal)) {
            return redirect()->back()->withInput()->with('error', 
                'Gagal: Laporan bulan tersebut sudah ditutup. Batas pengisian/edit adalah tanggal 4 bulan berikutnya.'
            );
        }

        // =====================================================================
        // VALIDASI 3: CEK MINGGUAN (1 Minggu 1 Laporan)
        // =====================================================================
        $cek = $this->laporanModel->cekLaporanMingguIni($kelompok['id'], $tanggal);
        if ($cek) {
            return redirect()->back()->withInput()->with('error', 
                'Gagal: Laporan untuk pekan tanggal tersebut sudah dibuat sebelumnya.'
            );
        }

        // Simpan Data
        $this->laporanModel->save([
            'kelompok_id'        => $kelompok['id'],
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

    // --- PRIVATE HELPER ---

    private function getKelompokUser($userId)
    {
        return $this->db->table('kelompok')
            ->select('kelompok.*, p.nama as nama_pembina, s.nama as nama_sekertaris')
            ->join('users p', 'p.id = kelompok.pembina_id')
            ->join('users s', 's.id = kelompok.sekertaris_id', 'left')
            ->groupStart()
                ->where('kelompok.pembina_id', $userId)
                ->orWhere('kelompok.sekertaris_id', $userId)
            ->groupEnd()
            ->get()->getRowArray();
    }

    // Logic: Jika tanggal input > Hari Ini = Error
    private function cekMasaDepan($tanggalInput)
    {
        return $tanggalInput > date('Y-m-d');
    }

    // Logic: Deadline tanggal 4 bulan berikutnya
    private function cekApakahTerlambat($tanggalInput)
    {
        $today = date('Y-m-d');

        // Ambil Bulan & Tahun dari tanggal laporan yang diinput user
        $bulanLaporan = date('m', strtotime($tanggalInput));
        $tahunLaporan = date('Y', strtotime($tanggalInput));

        // Hitung Deadline: Tgl 1 bulan laporan + 1 bulan + 3 hari = Tgl 4 bulan depan
        // Contoh: Laporan Oktober (10), Deadline = 4 November.
        $deadline = date('Y-m-d', strtotime("$tahunLaporan-$bulanLaporan-01 +1 month +3 days"));

        // Jika hari ini lebih besar dari deadline, maka terlambat.
        return $today > $deadline;
    }
}