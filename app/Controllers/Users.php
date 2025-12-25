<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Panggil method di model, lalu chain dengan paginate
        // 5 data per halaman
        $data = [
            'users' => $this->userModel->getUsersWithRoles()->paginate(10, 'users'), 
            'pager' => $this->userModel->pager
        ];

        return view('users/index', $data);
    }

    // Menampilkan Form Tambah User
    public function create()
    {
        // Kita butuh data Role untuk ditampilkan di checkbox
        $roles = $this->db->table('roles')->get()->getResultArray();

        $data = [
            'roles' => $roles
        ];
        return view('users/create', $data);
    }

    // Proses Simpan Data
    public function store()
    {
        // 1. Validasi Input (Hanya Nama, Gender, Jenjang, Role yang wajib diisi user)
        // Username & Password dihapus dari validasi input karena auto-generate
        if (!$this->validate([
            'nama'     => 'required',
            'role'     => 'required', 
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $namaLengkap = $this->request->getVar('nama');
        
        // 2. Generate Username Otomatis
        // Bersihkan nama, jadikan huruf kecil, pecah berdasarkan spasi
        $cleanName = strtolower(trim($namaLengkap));
        $parts = explode(' ', $cleanName);
        
        // Ambil nama depan
        $usernameBase = $parts[0];
        // Jika ada kata kedua, gabungkan
        if (isset($parts[1])) {
            $usernameBase .= $parts[1];
        }
        // Hapus karakter aneh selain huruf/angka agar aman
        $usernameBase = preg_replace('/[^a-z0-9]/', '', $usernameBase);

        // 3. Cek Keunikan Username (Looping)
        $usernameFinal = $usernameBase;
        $counter = 1;
        
        // Cek di database apakah username sudah ada
        // Kita gunakan loop while untuk terus mengecek sampai dapat yang unik
        while ($this->userModel->where('username', $usernameFinal)->countAllResults() > 0) {
            $usernameFinal = $usernameBase . $counter; // misal: budisantoso1
            $counter++;
        }

        // 4. Set Default Password
        $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);

        // 5. Simpan ke Database
        $userData = [
            'nama'     => $namaLengkap,
            'username' => $usernameFinal,     // Hasil generate
            'password' => $defaultPassword,   // Default 123456
            'gender'   => $this->request->getVar('gender'),
            'jenjang'  => $this->request->getVar('jenjang'),
        ];

        $this->userModel->insert($userData);
        $newUserId = $this->userModel->getInsertID();

        // 6. Simpan Role
        $selectedRoles = $this->request->getVar('role');
        $roleData = [];
        foreach ($selectedRoles as $roleId) {
            $roleData[] = [
                'user_id' => $newUserId,
                'role_id' => $roleId
            ];
        }
        $this->db->table('user_roles')->insertBatch($roleData);

        // Beri pesan sukses dengan info username yang terbentuk
        return redirect()->to('/users')->with('success', "User berhasil ditambahkan! Username: <b>$usernameFinal</b>, Password: <b>123456</b>");
    }

    // --- FITUR DELETE ---
    public function delete($id)
    {
        // Cek dulu apakah user ada
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('errors', ['User tidak ditemukan']);
        }

        // Hapus user
        // Karena kita pakai 'ON DELETE CASCADE' di database (table user_roles), 
        // maka role-nya akan otomatis terhapus juga.
        $this->userModel->delete($id);

        return redirect()->to('/users')->with('success', 'User berhasil dihapus.');
    }

    // --- FITUR RESET PASSWORD ---
    public function resetPassword($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('errors', ['User tidak ditemukan']);
        }

        // Set password ke default '123456'
        $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);

        // Update database
        $this->userModel->update($id, ['password' => $defaultPassword]);

        return redirect()->back()->with('success', "Password untuk user <b>{$user['username']}</b> berhasil direset ke 123456.");
    }

    // --- MENAMPILKAN FORM EDIT ---
    public function edit($id)
    {
        // 1. Ambil data user
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('errors', ['User tidak ditemukan']);
        }

        // 2. Ambil semua role yang tersedia (untuk pilihan checkbox)
        $roles = $this->db->table('roles')->get()->getResultArray();

        // 3. Ambil role yang dimiliki user saat ini (agar checkbox tercentang)
        $currentRoles = $this->db->table('user_roles')
                                 ->where('user_id', $id)
                                 ->get()
                                 ->getResultArray();
        
        // Ubah jadi array sederhana berisi ID saja: [1, 2]
        $userRoleIds = array_column($currentRoles, 'role_id');

        $data = [
            'user' => $user,
            'roles' => $roles,
            'user_role_ids' => $userRoleIds
        ];

        return view('users/edit', $data);
    }

    // --- PROSES UPDATE DATA ---
    public function update($id)
    {
        // 1. Validasi
        if (!$this->validate([
            'nama' => 'required',
            'role' => 'required', // Wajib pilih minimal 1 role
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Update Data Profil (Tabel users)
        // Username & Password tidak diupdate di sini
        $updateData = [
            'nama'    => $this->request->getVar('nama'),
            'gender'  => $this->request->getVar('gender'),
            'jenjang' => $this->request->getVar('jenjang'),
        ];
        $this->userModel->update($id, $updateData);

        // 3. Update Role (Tabel user_roles)
        // Cara paling aman: Hapus semua role lama user ini, lalu insert yang baru
        
        // A. Hapus role lama
        $this->db->table('user_roles')->where('user_id', $id)->delete();

        // B. Insert role baru
        $selectedRoles = $this->request->getVar('role');
        $roleData = [];
        foreach ($selectedRoles as $roleId) {
            $roleData[] = [
                'user_id' => $id,
                'role_id' => $roleId
            ];
        }
        $this->db->table('user_roles')->insertBatch($roleData);

        return redirect()->to('/users')->with('success', 'Data user berhasil diperbarui.');
    }
}