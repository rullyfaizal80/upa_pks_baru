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

    // 1. TAMPILKAN LIST LAPORAN
    public function index()
    {
        $userId = session()->get('id');
        $kelompok = $this->getKelompokUser($userId); 

        if (!$kelompok) {
            return redirect()->to('/dashboard')->with('error', 'Anda bukan pengurus kelompok manapun.');
        }

        $laporan = $this->laporanModel
            ->where('kelompok_id', $kelompok['id'])
            ->orderBy('tanggal', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Riwayat Laporan UPA',
            'kelompok' => $kelompok,
            'laporan' => $laporan
        ];

        return view('kegiatan/index', $data);
    }

    // 2. FORMULIR PEMBUATAN
    public function create()
    {
        $userId = session()->get('id');
        $kelompok = $this->getKelompokUser($userId);

        if (!$kelompok) return redirect()->back();

        $data = [
            'title' => 'Buat Laporan Baru',
            'kelompok' => $kelompok
        ];

        return view('kegiatan/create', $data);
    }

    // 3. PROSES SIMPAN DATA
    public function store()
    {
        $userId = session()->get('id');
        $kelompok = $this->getKelompokUser($userId); // Ambil data kelompok TERBARU
        
        if (!$kelompok) return redirect()->back();

        $tanggal = $this->request->getPost('tanggal');

        // A. Validasi Mingguan (1 Minggu 1 Laporan)
        $cek = $this->laporanModel->cekLaporanMingguIni($kelompok['id'], $tanggal);
        if ($cek) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Laporan untuk pekan tanggal tersebut sudah dibuat sebelumnya.');
        }

        // B. Simpan ke Database
        $this->laporanModel->save([
            'kelompok_id'        => $kelompok['id'],
            'user_id'            => $userId,
            
            // SNAPSHOT: Simpan ID Pembina & Sekertaris SAAT INI
            // Agar jika bulan depan diganti, data lama tetap aman.
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
    // Mencari kelompok dimana User Login bertindak sebagai Ketua ATAU Sekertaris
    // --- PRIVATE HELPER ---
    private function getKelompokUser($userId)
    {
        // Kita gunakan alias: 'p' untuk user pembina, 's' untuk user sekertaris
        return $this->db->table('kelompok')
            ->select('kelompok.*, p.nama as nama_pembina, s.nama as nama_sekertaris')
            
            // Join pertama: Ambil Nama Pembina
            ->join('users p', 'p.id = kelompok.pembina_id')
            
            // Join kedua: Ambil Nama Sekertaris (pakai left join agar jika kosong tidak error)
            ->join('users s', 's.id = kelompok.sekertaris_id', 'left')
            
            ->groupStart()
                ->where('kelompok.pembina_id', $userId)
                ->orWhere('kelompok.sekertaris_id', $userId)
                // ->orWhere('kelompok.ketua_id', $userId) // Jika nanti ada ketua
            ->groupEnd()
            ->get()->getRowArray();
    }
}