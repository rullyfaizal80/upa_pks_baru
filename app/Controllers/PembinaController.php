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
        // Menggunakan Model LaporanAmalanModel yang sudah Anda miliki
        $this->laporanModel = new LaporanAmalanModel(); 
    }

    // =================================================================
    // 1. DASHBOARD PEMBINA (LIST KELOMPOK)
    // =================================================================
    public function index()
    {
        $userId = session()->get('id');
        
        // 1. Ambil daftar kelompok dimana user ini adalah PEMBINA-nya
        $kelompokList = $this->db->table('kelompok')
            ->where('pembina_id', $userId) 
            ->get()->getResultArray();

        // 2. Loop setiap kelompok untuk melengkapi data (Nama Pembina, Sekertaris, & Anggota)
        foreach ($kelompokList as &$k) {
            
            // A. Cari Nama Pembina (dari kolom pembina_id)
            $pembina = $this->db->table('users')
                ->select('nama')
                ->where('id', $k['pembina_id'])
                ->get()->getRowArray();
            $k['nama_pembina'] = $pembina ? $pembina['nama'] : '-';

            // B. Cari Nama Sekertaris
            // Menggunakan '?? 0' untuk mencegah error jika kolom sekertaris_id null/kosong
            $sekertaris = $this->db->table('users')
                ->select('nama')
                ->where('id', $k['sekertaris_id'] ?? 0) 
                ->get()->getRowArray();
            $k['nama_sekertaris'] = $sekertaris ? $sekertaris['nama'] : '- Belum ditentukan -';

            // C. Cari Daftar Anggota
            // PERBAIKAN: Menggunakan tabel 'anggota_kelompok'
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
    // 2. DETAIL MONITORING (Mingguan & Bulanan)
    // =================================================================
    public function monitoring($kelompokId)
    {
        // -----------------------------------------------------------
        // A. VALIDASI KELOMPOK
        // -----------------------------------------------------------
        $kelompok = $this->db->table('kelompok')->where('id', $kelompokId)->get()->getRowArray();
        
        if (!$kelompok) {
            return redirect()->to('/pembina')->with('error', 'Kelompok tidak ditemukan');
        }

        // -----------------------------------------------------------
        // B. DATA MINGGUAN (STATUS LAPORAN)
        // -----------------------------------------------------------
        
        // 1. Ambil filter tanggal (default hari ini) & Hitung Periode
        $filterTanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        helper('laporan'); // Pastikan helper sudah dibuat
        $periode = hitungPeriodeMingguan($filterTanggal);

        // 2. Ambil Semua Anggota Kelompok
        $anggotaList = $this->db->table('anggota_kelompok')
            ->select('users.id, users.nama, users.jenjang')
            ->join('users', 'users.id = anggota_kelompok.user_id')
            ->where('anggota_kelompok.kelompok_id', $kelompokId)
            ->orderBy('users.nama', 'ASC')
            ->get()->getResultArray();

        // 3. Ambil Laporan yang masuk pada pekan tersebut
        $laporanMingguan = $this->laporanModel
            ->where('kelompok_id', $kelompokId)
            ->where('periode_mulai', $periode['mulai'])
            ->findAll();

        // 4. Mapping Status (Siapa yang sudah, siapa yang belum)
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
        // C. DATA BULANAN (RATA-RATA AMALAN)
        // -----------------------------------------------------------

        // 1. Ambil filter bulan & tahun (default saat ini)
        $filterBulan = $this->request->getGet('bulan') ?? date('m');
        $filterTahun = $this->request->getGet('tahun') ?? date('Y');

        // 2. Query Rata-rata per User (Termasuk Tilawah / Amalan 3)
        $statsBulanan = $this->laporanModel
            ->select('user_id')
            ->selectAvg('amalan_1', 'avg1') // Jamaah
            ->selectAvg('amalan_2', 'avg2') // Qiyamul Lail
            ->selectAvg('amalan_3', 'avg3') // Tilawah (SUDAH DIMASUKKAN KEMBALI)
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

        // 3. Mapping Data Rata-rata ke Anggota
        $rekapBulanan = [];
        foreach ($anggotaList as $anggota) {
            $stats = null;
            
            // Cari data statistik milik user ini
            foreach ($statsBulanan as $s) {
                if ($s['user_id'] == $anggota['id']) {
                    $stats = $s;
                    break;
                }
            }

            $rekapBulanan[] = [
                'anggota' => $anggota,
                'stats'   => $stats // Berisi avg1, avg2, dst.. atau null
            ];
        }

        // -----------------------------------------------------------
        // D. RETURN DATA KE VIEW
        // -----------------------------------------------------------
        $data = [
            'title'          => 'Monitoring Anggota',
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
}