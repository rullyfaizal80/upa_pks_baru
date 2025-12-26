<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Cek Login (Wajib Login dulu)
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        // 2. Jika tidak ada argumen role yang diminta, loloskan saja
        if (empty($arguments)) {
            return;
        }

        // 3. Ambil Role user dari session
        // (Sesuai kode Auth.php Anda sebelumnya, role disimpan dalam array session 'roles')
        $userRoles = session()->get('roles'); 

        // 4. Cek apakah salah satu role user cocok dengan argumen yang diminta
        // Argumen dikirim dari Routes, misal: ['pembina', 'admin']
        $isAllowed = false;
        
        foreach ($arguments as $roleNeeded) {
            if (in_array($roleNeeded, $userRoles)) {
                $isAllowed = true;
                break; // Jika sudah ketemu satu yang cocok, stop looping
            }
        }

        // 5. Jika tidak punya hak akses, tendang ke Dashboard
        if (!$isAllowed) {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak. Anda tidak memiliki izin untuk masuk ke halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah request
    }
}