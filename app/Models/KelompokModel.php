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
        // Kita gunakan alias 'p' untuk pembina dan 's' untuk sekertaris
        return $this->select('kelompok.*, p.nama as nama_pembina, s.nama as nama_sekertaris')
                    ->join('users p', 'p.id = kelompok.pembina_id')
                    ->join('users s', 's.id = kelompok.sekertaris_id')
                    ->orderBy('kelompok.id', 'DESC');
    }
}