<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LaporanAmalanModel; 
use App\Models\UserModel;

class PembinaController extends BaseController
{
    protected $db;
    protected $laporanModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->laporanModel = new LaporanAmalanModel(); 
    }

    // =================================================================
    // 1. DASHBOARD (LIST KELOMPOK)
    // =================================================================
    public function index()
    {
        $userId = session()->get('id');
        $userRoles = session()->get('roles') ?? []; // Ambil role dari session

        // Mulai Query Builder
        $builder = $this->db->table('kelompok');

        // LOGIKA BARU: 
        // Jika User adalah 'ketua' atau 'admin', Tampilkan SEMUA kelompok.
        // Jika BUKAN, maka filter berdasarkan pembina_id atau sekertaris_id.
        $isKetua = in_array('ketua', $userRoles) || in_array('admin', $userRoles);

        if (!$isKetua) {
            $builder->groupStart()
                ->where('pembina_id', $userId)
                ->orWhere('sekertaris_id', $userId)
            ->groupEnd();
        }

        // Eksekusi Query
        $kelompokList = $builder->orderBy('nama_kelompok', 'ASC')->get()->getResultArray();

        // 2. Loop setiap kelompok untuk melengkapi data
        foreach ($kelompokList as &$k) {
            
            // A. Cari Nama Pembina
            $pembina = $this->db->table('users')
                ->select('nama')
                ->where('id', $k['pembina_id'])
                ->get()->getRowArray();
            $k['nama_pembina'] = $pembina ? $pembina['nama'] : '-';

            // B. Cari Nama Sekertaris
            $sekertaris = $this->db->table('users')
                ->select('nama')
                ->where('id', $k['sekertaris_id'] ?? 0) 
                ->get()->getRowArray();
            $k['nama_sekertaris'] = $sekertaris ? $sekertaris['nama'] : '- Belum ditentukan -';

            // C. Cari Daftar Anggota
            $anggota = $this->db->table('anggota_kelompok')
                ->select('users.nama, users.jenjang')
                ->join('users', 'users.id = anggota_kelompok.user_id')
                ->where('anggota_kelompok.kelompok_id', $k['id'])
                ->orderBy('users.nama', 'ASC')
                ->get()->getResultArray();
            
            $k['list_anggota'] = $anggota;
        }

        $data = [
            'title' => 'Dashboard Pembinaan',
            'kelompok_list' => $kelompokList
        ];

        return view('pembina/index', $data);
    }

    // =================================================================
    // 2. DETAIL MONITORING (MINGGUAN & BULANAN)
    // =================================================================
    public function monitoring($kelompokId)
    {
        // A. Ambil Data Kelompok
        $kelompok = $this->db->table('kelompok')->where('id', $kelompokId)->get()->getRowArray();
        
        if (!$kelompok) {
            return redirect()->to('/pembina')->with('error', 'Kelompok tidak ditemukan');
        }

        // [KEAMANAN & HAK AKSES]
        $userId = session()->get('id');
        $userRoles = session()->get('roles') ?? [];
        
        // Cek apakah dia Ketua/Admin?
        $isKetua = in_array('ketua', $userRoles) || in_array('admin', $userRoles);
        // Cek apakah dia Pengurus (Pembina/Sekertaris) kelompok ini?
        $isPengurus = ($kelompok['pembina_id'] == $userId || $kelompok['sekertaris_id'] == $userId);

        // Jika BUKAN Ketua DAN BUKAN Pengurus kelompok tsb -> TENDANG
        if (!$isKetua && !$isPengurus) {
            return redirect()->to('/pembina')->with('error', 'Akses Ditolak. Anda tidak memiliki izin melihat kelompok ini.');
        }

        // -----------------------------------------------------------
        // BAGIAN 1: DATA MINGGUAN (STATUS LAPORAN)
        // -----------------------------------------------------------
        
        // Ambil filter tanggal dari input URL, default hari ini
        $filterTanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        
        helper('laporan'); 
        $periode = hitungPeriodeMingguan($filterTanggal);

        // Ambil Semua Anggota di Kelompok ini
        $anggotaList = $this->db->table('anggota_kelompok')
            ->select('users.id, users.nama, users.jenjang')
            ->join('users', 'users.id = anggota_kelompok.user_id')
            ->where('anggota_kelompok.kelompok_id', $kelompokId)
            ->orderBy('users.nama', 'ASC')
            ->get()->getResultArray();

        // Ambil Laporan yang SUDAH MASUK di periode ini
        $laporanMingguan = $this->laporanModel
            ->where('kelompok_id', $kelompokId)
            ->where('periode_mulai', $periode['mulai'])
            ->findAll();

        // Mapping Status
        $rekapMingguan = [];
        foreach ($anggotaList as $anggota) {
            $status = 'Belum Lapor';
            $detail = null;

            foreach ($laporanMingguan as $lap) {
                if ($lap['user_id'] == $anggota['id']) {
                    $status = 'Sudah Lapor';
                    $detail = $lap;
                    break;
                }
            }
            
            $rekapMingguan[] = [
                'anggota' => $anggota,
                'status'  => $status,
                'laporan' => $detail
            ];
        }

        // -----------------------------------------------------------
        // BAGIAN 2: DATA BULANAN (RATA-RATA AMALAN)
        // -----------------------------------------------------------

        // Ambil filter bulan/tahun, default sekarang
        $filterBulan = $this->request->getGet('bulan') ?? date('m');
        $filterTahun = $this->request->getGet('tahun') ?? date('Y');

        // Query Rata-rata per User di Bulan tersebut
        $statsBulanan = $this->laporanModel
            ->select('user_id')
            ->selectAvg('amalan_1', 'avg1') // Jamaah
            ->selectAvg('amalan_2', 'avg2') // Qiyamul Lail
            ->selectAvg('amalan_3', 'avg3') // Tilawah
            ->selectAvg('amalan_4', 'avg4') // Shaum
            ->selectAvg('amalan_5', 'avg5') // Matsurat
            ->selectAvg('amalan_6', 'avg6') // Dhuha
            ->selectAvg('amalan_7', 'avg7') // Olahraga
            ->selectAvg('amalan_8', 'avg8') // Istighfar
            ->selectAvg('amalan_9', 'avg9') // Shalawat
            ->where('kelompok_id', $kelompokId)
            ->where('MONTH(periode_mulai)', $filterBulan)
            ->where('YEAR(periode_mulai)', $filterTahun)
            ->groupBy('user_id')
            ->findAll();

        // Gabungkan Data Rata-rata ke list Anggota
        $rekapBulanan = [];
        foreach ($anggotaList as $anggota) {
            $stats = null;
            
            // Cari data statistik user ini
            foreach ($statsBulanan as $s) {
                if ($s['user_id'] == $anggota['id']) {
                    $stats = $s;
                    break;
                }
            }

            $rekapBulanan[] = [
                'anggota' => $anggota,
                'stats'   => $stats 
            ];
        }

        // -----------------------------------------------------------
        // RETURN VIEW
        // -----------------------------------------------------------
        $data = [
            'title'          => 'Dashboard Pembinaan', // Title sedikit disesuaikan
            'kelompok'       => $kelompok,
            
            // Data Mingguan
            'periode'        => $periode,
            'filter_tanggal' => $filterTanggal,
            'rekap_mingguan' => $rekapMingguan,

            // Data Bulanan
            'filter_bulan'   => $filterBulan,
            'filter_tahun'   => $filterTahun,
            'rekap_bulanan'  => $rekapBulanan
        ];

        return view('pembina/monitoring', $data);
    }

    // =================================================================
    // 3. LAPORAN KESELURUHAN (KHUSUS KETUA)
    // =================================================================
    public function laporanKetua()
    {
        // 1. Cek Hak Akses (Hanya Ketua & Admin)
        $userRoles = session()->get('roles') ?? [];
        if (!in_array('ketua', $userRoles) && !in_array('admin', $userRoles)) {
            return redirect()->to('/pembina')->with('error', 'Halaman ini khusus untuk Ketua.');
        }

        // 2. Ambil Filter Bulan & Tahun
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // -----------------------------------------------------------
        // A. DATA DEMOGRAFI (JUMLAH-JUMLAHAN)
        // -----------------------------------------------------------
        
        // 1. Total Kelompok
        $totalKelompok = $this->db->table('kelompok')->countAllResults();

        // 2. Total Pembina (Muda & Pratama)
        // Kita hitung user yang sedang menjabat sebagai pembina di tabel kelompok (distinct)
        $pembinaStats = $this->db->table('kelompok')
            ->select('users.jenjang, COUNT(DISTINCT kelompok.pembina_id) as total')
            ->join('users', 'users.id = kelompok.pembina_id')
            ->groupBy('users.jenjang')
            ->get()->getResultArray();
        
        // Parsing hasil query pembina
        $pembinaMuda = 0; $pembinaPratama = 0;
        foreach ($pembinaStats as $p) {
            if ($p['jenjang'] == 'Muda') $pembinaMuda = $p['total'];
            if ($p['jenjang'] == 'Pratama') $pembinaPratama = $p['total'];
        }

        // 3. Total Sekertaris (Muda & Pratama)
        $sekertarisStats = $this->db->table('kelompok')
            ->select('users.jenjang, COUNT(DISTINCT kelompok.sekertaris_id) as total')
            ->join('users', 'users.id = kelompok.sekertaris_id')
            ->where('kelompok.sekertaris_id !=', 0) // Pastikan ada sekertarisnya
            ->groupBy('users.jenjang')
            ->get()->getResultArray();

        $sekertarisMuda = 0; $sekertarisPratama = 0;
        foreach ($sekertarisStats as $s) {
            if ($s['jenjang'] == 'Muda') $sekertarisMuda = $s['total'];
            if ($s['jenjang'] == 'Pratama') $sekertarisPratama = $s['total'];
        }

        // 4. Total Anggota (Gender & Jenjang)
        // Hitung anggota yang SUDAH masuk kelompok (ada di tabel anggota_kelompok)
        $anggotaQuery = $this->db->table('anggota_kelompok')
            ->join('users', 'users.id = anggota_kelompok.user_id')
            ->select('users.jenjang, users.gender'); // Pastikan kolom gender ada di tabel users (L/P)
        
        $rawAnggota = $anggotaQuery->get()->getResultArray();

        $totalAnggota = count($rawAnggota);
        $anggotaL = 0; $anggotaP = 0;
        $anggotaMuda = 0; $anggotaPratama = 0;

        foreach ($rawAnggota as $a) {
            // Hitung Gender
            if ($a['gender'] == 'L') $anggotaL++;
            else $anggotaP++; // Asumsi selain L adalah P

            // Hitung Jenjang
            if ($a['jenjang'] == 'Muda') $anggotaMuda++;
            else $anggotaPratama++; // Asumsi Pratama (atau lainnya)
        }

        // -----------------------------------------------------------
        // B. DATA STATISTIK AMALAN (RATA-RATA)
        // -----------------------------------------------------------
        
        // 1. Statistik Keseluruhan
        $statAll = $this->getStatistikAmalan($bulan, $tahun, null);

        // 2. Statistik Muda
        $statMuda = $this->getStatistikAmalan($bulan, $tahun, 'Muda');

        // 3. Statistik Pratama
        $statPratama = $this->getStatistikAmalan($bulan, $tahun, 'Pratama');

        $data = [
            'title' => 'Laporan Bulanan Ketua',
            'filter_bulan' => $bulan,
            'filter_tahun' => $tahun,
            
            // Demografi
            'total_kelompok' => $totalKelompok,
            'pembina_muda' => $pembinaMuda, 'pembina_pratama' => $pembinaPratama,
            'sekertaris_muda' => $sekertarisMuda, 'sekertaris_pratama' => $sekertarisPratama,
            'total_anggota' => $totalAnggota,
            'anggota_l' => $anggotaL, 'anggota_p' => $anggotaP,
            'anggota_muda' => $anggotaMuda, 'anggota_pratama' => $anggotaPratama,

            // Statistik
            'stat_all' => $statAll,
            'stat_muda' => $statMuda,
            'stat_pratama' => $statPratama
        ];

        return view('pembina/laporan_ketua', $data);
    }

    // --- PRIVATE HELPER: MENGHITUNG RATA-RATA ---
    private function getStatistikAmalan($bulan, $tahun, $jenjang = null)
    {
        $builder = $this->laporanModel
            ->join('users', 'users.id = laporan_amalan.user_id')
            ->where('MONTH(periode_mulai)', $bulan)
            ->where('YEAR(periode_mulai)', $tahun);

        if ($jenjang) {
            $builder->where('users.jenjang', $jenjang);
        }

        // LOGIKA KHUSUS AMALAN 1 (SHOLAT JAMAAH)
        // Hanya hitung rata-rata jika gender = 'L'
        // Kita pakai syntax SQL CASE WHEN didalam AVG
        $builder->select('AVG(CASE WHEN users.gender = "L" THEN amalan_1 ELSE NULL END) as avg1');
        
        // Amalan 2-9 Normal (Semua Gender)
        $builder->selectAvg('amalan_2', 'avg2');
        $builder->selectAvg('amalan_3', 'avg3');
        $builder->selectAvg('amalan_4', 'avg4');
        $builder->selectAvg('amalan_5', 'avg5');
        $builder->selectAvg('amalan_6', 'avg6');
        $builder->selectAvg('amalan_7', 'avg7');
        $builder->selectAvg('amalan_8', 'avg8');
        $builder->selectAvg('amalan_9', 'avg9');

        return $builder->get()->getRowArray();
    }
}