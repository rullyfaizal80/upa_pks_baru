<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanAmalanModel extends Model
{
    protected $table            = 'laporan_amalan';
    protected $primaryKey       = 'id';
    
    // Kolom yang boleh diisi/diupdate oleh aplikasi
    protected $allowedFields    = [
        'user_id', 
        'kelompok_id', 
        'tanggal_upa', 
        'periode_mulai', 
        'periode_selesai',
        'amalan_1', // Sholat Jamaah
        'amalan_2', // Qiyamul Lail
        'amalan_3', // Tilawah
        'amalan_4', // Shaum
        'amalan_5', // Matsurat
        'amalan_6', // Dhuha
        'amalan_7', // Olahraga
        'amalan_8', // Istighfar
        'amalan_9'  // Shalawat
    ];

    // Aktifkan timestamp agar created_at dan updated_at terisi otomatis
    protected $useTimestamps    = true;

    /**
     * Cek apakah user sudah pernah mengisi laporan 
     * pada periode pekan tertentu (agar tidak dobel).
     */
    public function sudahIsi($userId, $periodeMulai)
    {
        // Hitung jumlah data berdasarkan User ID dan Tanggal Mulai Pekan
        $jumlah = $this->where('user_id', $userId)
                       ->where('periode_mulai', $periodeMulai)
                       ->countAllResults();

        // Jika lebih dari 0, berarti sudah ada (return true)
        return $jumlah > 0;
    }
}