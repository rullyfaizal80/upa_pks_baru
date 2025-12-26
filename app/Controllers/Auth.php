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

    // ... method logout() yang sudah ada ...

    // =================================================================
    // 1. TAMPILKAN FORM GANTI PASSWORD
    // =================================================================
    public function gantiPassword()
    {
        // Pastikan user sudah login
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Ganti Password',
            'validation' => \Config\Services::validation()
        ];
        // Pastikan Anda sudah membuat view: app/Views/auth/ganti_password.php
        return view('auth/ganti_password', $data);
    }

    // =================================================================
    // 2. PROSES UPDATE PASSWORD
    // =================================================================
    public function updatePassword()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        // A. Validasi Input
        // Simpan rules dalam variabel agar rapi
        $rules = [
            'password_lama' => [
                'rules'  => 'required',
                'errors' => ['required' => 'Password lama wajib diisi.']
            ],
            'password_baru' => [
                'rules'  => 'required', 
                'errors' => ['required' => 'Password baru wajib diisi.']
            ],
            'konfirmasi_password' => [
                'rules'  => 'required|matches[password_baru]', // INI KUNCI VALIDASI MATCHING
                'errors' => [
                    'required' => 'Konfirmasi password wajib diisi.',
                    'matches'  => 'Password baru dan konfirmasi tidak sama.' // Pesan Error
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            // PENTING: withInput() mengembalikan apa yang diketik
            // with('errors') mengembalikan pesan error spesifik
            return redirect()->to('/ganti-password')->withInput()->with('errors', $this->validator->getErrors());
        }

        // B. Cek Password Lama vs Database
        $passwordLamaInput = $this->request->getPost('password_lama');
        
        if (!password_verify($passwordLamaInput, $user['password'])) {
            // PENTING: Kirim error manual array agar bisa dibaca View sama seperti error validasi
            return redirect()->to('/ganti-password')->withInput()->with('errors', ['password_lama' => 'Password lama salah!']);
        }

        // C. Update Password Baru
        $passwordBaru = $this->request->getPost('password_baru');
        
        $userModel->update($userId, [
            'password' => password_hash($passwordBaru, PASSWORD_DEFAULT)
        ]);

        return redirect()->to('/dashboard')->with('success', 'Password berhasil diubah.');
    }
}