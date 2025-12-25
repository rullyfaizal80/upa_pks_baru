<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['username', 'password', 'nama', 'gender', 'jenjang']; // Sesuaikan field 'nama' dengan database Anda (apakah 'nama' atau 'nama_lengkap'?)
    
    // Pastikan ini sesuai kolom database Anda. 
    // Di SQL dump anda kolomnya 'nama_lengkap', tapi di view login 'nama'.
    // Saya pakai 'nama' disini, tolong sesuaikan jika error.

    protected $useTimestamps    = false; 

    // Method ini sekarang mengembalikan $this (Object Model) agar bisa dichain dengan ->paginate()
    public function getUsersWithRoles()
    {
        $this->select('users.*, GROUP_CONCAT(roles.role_name) as role_names');
        $this->join('user_roles', 'user_roles.user_id = users.id', 'left');
        $this->join('roles', 'roles.id = user_roles.role_id', 'left');
        $this->groupBy('users.id');
        
        return $this; 
    }
}