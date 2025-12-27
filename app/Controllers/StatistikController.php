<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class StatistikController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Ambil filter tahun/bulan
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $bulan = $this->request->getGet('bulan') ?? date('m');

        // 1. STATISTIK KARTU ATAS
        $totalKelompok = $this->db->table('kelompok')->countAllResults();
        
        $sudahLapor = $this->db->table('laporan_upa')
            ->select('kelompok_id')
            ->where('YEAR(tanggal)', $tahun)
            ->where('MONTH(tanggal)', $bulan)
            ->distinct()
            ->countAllResults();

        $belumLapor = $totalKelompok - $sudahLapor;
        $persentase = ($totalKelompok > 0) ? round(($sudahLapor / $totalKelompok) * 100) : 0;

        // 2. DATA TABEL MONITORING
        $sql = "SELECT 
                    k.id, 
                    k.nama_kelompok, 
                    p.nama as nama_pembina,
                    (SELECT COUNT(*) FROM laporan_upa l 
                     WHERE l.kelompok_id = k.id 
                     AND YEAR(l.tanggal) = ? AND MONTH(l.tanggal) = ?) as jumlah_laporan,
                    (SELECT AVG(l.total_hadir) FROM laporan_upa l 
                     WHERE l.kelompok_id = k.id 
                     AND YEAR(l.tanggal) = ? AND MONTH(l.tanggal) = ?) as rata_rata_hadir
                FROM kelompok k
                JOIN users p ON p.id = k.pembina_id
                ORDER BY jumlah_laporan DESC, k.nama_kelompok ASC";

        $monitoring = $this->db->query($sql, [$tahun, $bulan, $tahun, $bulan])->getResultArray();

        $data = [
            'title'         => 'Statistik & Monitoring UPA',
            'tahun'         => $tahun,
            'bulan'         => $bulan,
            'totalKelompok' => $totalKelompok,
            'sudahLapor'    => $sudahLapor,
            'belumLapor'    => $belumLapor,
            'persentase'    => $persentase,
            'monitoring'    => $monitoring
        ];

        return view('statistik/index', $data);
    }

    // [BARU] Method untuk mengambil detail laporan per kelompok (Respon JSON untuk Modal)
    public function getDetailJson($kelompokId)
    {
        $tahun = $this->request->getGet('tahun');
        $bulan = $this->request->getGet('bulan');

        // Ambil data laporan lengkap untuk kelompok tersebut di bulan yang dipilih
        $data = $this->db->table('laporan_upa')
            ->where('kelompok_id', $kelompokId)
            ->where('YEAR(tanggal)', $tahun)
            ->where('MONTH(tanggal)', $bulan)
            ->orderBy('tanggal', 'ASC')
            ->get()
            ->getResultArray();

        // Ambil nama kelompok untuk judul modal
        $kelompok = $this->db->table('kelompok')->select('nama_kelompok')->where('id', $kelompokId)->get()->getRow();

        return $this->response->setJSON([
            'nama_kelompok' => $kelompok ? $kelompok->nama_kelompok : 'Tidak Diketahui',
            'laporan' => $data
        ]);
    }
}