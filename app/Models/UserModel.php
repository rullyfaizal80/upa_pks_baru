<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    
    // Sesuaikan field ini dengan kolom di tabel database Anda
    protected $allowedFields    = ['username', 'password', 'nama', 'gender', 'jenjang']; 
    
    protected $useTimestamps    = false; // Ubah ke true jika tabel users punya created_at/updated_at

    // Fungsi khusus untuk mengambil user beserta role-nya
    public function getUsersWithRoles()
    {
        $builder = $this->db->table('users');
        $builder->select('users.*, GROUP_CONCAT(roles.role_name) as role_names');
        
        // Join ke tabel pivot user_roles
        $builder->join('user_roles', 'user_roles.user_id = users.id', 'left');
        
        // Join ke tabel roles
        $builder->join('roles', 'roles.id = user_roles.role_id', 'left');
        
        $builder->groupBy('users.id');
        return $builder->get()->getResultArray();
    }
}