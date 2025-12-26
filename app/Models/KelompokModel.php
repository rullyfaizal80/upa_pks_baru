<?php

namespace App\Models;

use CodeIgniter\Model;

class KelompokModel extends Model
{
    protected $table            = 'kelompok';
    protected $primaryKey       = 'id';
    // Tambahkan sekertaris_id ke allowedFields
    protected $allowedFields    = ['nama_kelompok', 'pembina_id', 'sekertaris_id']; 
    protected $useTimestamps    = true;

    public function getKelompokLengkap()
    {
        // Select Data Kelompok, Nama Pembina, Nama Sekertaris
        $this->select('kelompok.*, p.nama as nama_pembina, s.nama as nama_sekertaris');
        
        // Select Subquery untuk menghitung jumlah anggota
        $this->select('(SELECT COUNT(*) FROM anggota_kelompok WHERE anggota_kelompok.kelompok_id = kelompok.id) as jumlah_anggota');
        
        // Join ke users untuk Pembina (alias p)
        $this->join('users p', 'p.id = kelompok.pembina_id');
        
        // Join ke users untuk Sekertaris (alias s)
        $this->join('users s', 's.id = kelompok.sekertaris_id');
        
        $this->orderBy('kelompok.id', 'DESC');
        
        return $this;
    }
}