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
    // 2. DETAIL MONITORING PER KELOMPOK
    // =================================================================
    public function monitoring($kelompokId)
    {
        // A. Ambil Data Kelompok
        $kelompok = $this->db->table('kelompok')->where('id', $kelompokId)->get()->getRowArray();
        
        if (!$kelompok) {
            return redirect()->to('/pembina')->with('error', 'Kelompok tidak ditemukan');
        }

        // B. Tentukan Periode (Default: Minggu ini)
        // Ambil filter tanggal dari input URL, jika tidak ada pakai hari ini
        $filterTanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        
        // Helper hitungPeriodeMingguan (Pastikan helper 'laporan' sudah diload di BaseController)
        helper('laporan'); 
        $periode = hitungPeriodeMingguan($filterTanggal);

        // C. Ambil Semua Anggota di Kelompok ini
        // PERBAIKAN: Menggunakan tabel 'anggota_kelompok'
        $anggotaList = $this->db->table('anggota_kelompok')
            ->select('users.id, users.nama, users.username, users.jenjang')
            ->join('users', 'users.id = anggota_kelompok.user_id')
            ->where('anggota_kelompok.kelompok_id', $kelompokId)
            ->orderBy('users.nama', 'ASC')
            ->get()->getResultArray();

        // D. Ambil Laporan yang SUDAH MASUK di periode ini (dari tabel laporan_amalan)
        $laporanMasuk = $this->laporanModel
            ->where('kelompok_id', $kelompokId)
            ->where('periode_mulai', $periode['mulai'])
            ->findAll();

        // E. LOGIKA GABUNGAN (Mapping Status)
        // Kita loop daftar anggota, cek apakah ID mereka ada di daftar laporan masuk
        $rekapData = [];
        
        foreach ($anggotaList as $anggota) {
            $status = 'Belum Lapor';
            $detailLaporan = null;

            // Cek satu per satu di array laporan
            foreach ($laporanMasuk as $lap) {
                if ($lap['user_id'] == $anggota['id']) {
                    $status = 'Sudah Lapor';
                    $detailLaporan = $lap;
                    break; // Stop loop jika sudah ketemu
                }
            }

            $rekapData[] = [
                'anggota' => $anggota,
                'status'  => $status,
                'laporan' => $detailLaporan
            ];
        }

        $data = [
            'title'         => 'Monitoring Anggota',
            'kelompok'      => $kelompok,
            'periode'       => $periode,
            'filter_tanggal'=> $filterTanggal,
            'rekap_data'    => $rekapData
        ];

        return view('pembina/monitoring', $data);
    }
}