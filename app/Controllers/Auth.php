<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        // Jika sudah login, lempar ke dashboard
        if (session()->get('is_logged_in')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function process()
    {
        $session = session();
        $model = new UserModel();
        
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        // 1. Cari User berdasarkan Username
        $user = $model->where('username', $username)->first();

        if ($user) {
            // 2. Cek Password (Hash)
            // Database Anda menggunakan hash bcrypt ($2y$...), jadi pakai password_verify
            if (password_verify($password, $user['password'])) {
                
                // 3. Ambil Role User tersebut
                // Kita query manual ke tabel pivot user_roles & roles
                $db = \Config\Database::connect();
                $builder = $db->table('user_roles');
                $builder->select('roles.role_name');
                $builder->join('roles', 'roles.id = user_roles.role_id');
                $builder->where('user_roles.user_id', $user['id']);
                $query = $builder->get()->getResultArray();

                // Ubah hasil query menjadi array simpel. Contoh: ['admin', 'pembina']
                $roles = array_column($query, 'role_name');

                // 4. Simpan ke Session
                $ses_data = [
                    'id'       => $user['id'],
                    'nama'     => $user['nama'],
                    'username' => $user['username'],
                    'roles'    => $roles, // Array role disimpan di sini
                    'is_logged_in' => true
                ];
                $session->set($ses_data);
                
                return redirect()->to('/dashboard');

            } else {
                $session->setFlashdata('error', 'Password salah.');
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('error', 'Username tidak ditemukan.');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}