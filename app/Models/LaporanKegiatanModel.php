<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanKegiatanModel extends Model
{
    protected $table            = 'laporan_upa'; // Sesuai nama tabel di SQL
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true; // Mengisi created_at otomatis

    protected $allowedFields    = [
        'kelompok_id', 
        'user_id', 
        'pembina_id',    // Field baru
        'sekertaris_id', // Field baru
        'tanggal', 
        'teknis_pelaksanaan', 
        'is_pembina_hadir', 
        'total_anggota', 
        'total_hadir', 
        'nama_tidak_hadir', 
        'materi'
    ];

    // Helper: Cek apakah minggu ini sudah ada laporan?
    public function cekLaporanMingguIni($kelompokId, $tanggalInput)
    {
        $tahun = date('Y', strtotime($tanggalInput));
        // Mode '1' pada date('W') menjadikan Senin sebagai awal minggu
        $minggu = date('W', strtotime($tanggalInput));

        return $this->where('kelompok_id', $kelompokId)
                    ->where("YEAR(tanggal)", $tahun)
                    ->where("WEEK(tanggal, 1)", $minggu) 
                    ->first();
    }
}