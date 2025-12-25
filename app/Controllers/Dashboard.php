<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // Cek apakah user sudah login (Manual proteksi sederhana)
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/login');
        }

        return view('dashboard/index');
    }
}