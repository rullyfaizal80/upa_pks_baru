<?php

namespace App\Models;

use CodeIgniter\Model;

class AnggotaKelompokModel extends Model
{
    protected $table            = 'anggota_kelompok';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['kelompok_id', 'user_id']; 
    // Kolom 'jabatan' kita hapus dari logic karena sekertaris sudah pindah ke tabel kelompok
    
    public function isUserInGroup($userId)
    {
        return $this->where('user_id', $userId)->countAllResults() > 0;
    }
    
    public function getMembersByKelompok($kelompokId)
    {
        // Join simpel ke tabel users untuk ambil nama anggota
        return $this->select('anggota_kelompok.id as id_anggota, users.nama, users.username, users.jenjang')
                    ->join('users', 'users.id = anggota_kelompok.user_id')
                    ->where('anggota_kelompok.kelompok_id', $kelompokId)
                    ->findAll();
    }
}