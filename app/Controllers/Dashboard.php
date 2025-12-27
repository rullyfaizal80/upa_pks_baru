<?php

namespace App\Controllers;

use App\Models\PengumumanModel;

class Dashboard extends BaseController
{
    protected $pengumumanModel;

    public function __construct()
    {
        $this->pengumumanModel = new PengumumanModel();
    }

    public function index()
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/login');

        // Ambil data (urutkan dari yang terbaru)
        $data['pengumuman_list'] = $this->pengumumanModel
                                    ->orderBy('tanggal', 'DESC')
                                    ->limit(5)
                                    ->findAll();

        $roles = session()->get('roles') ?? [];
        $data['can_manage_pengumuman'] = in_array('admin', $roles) || in_array('ketua', $roles);

        return view('dashboard/index', $data);
    }

    // --- TAMBAH ---
    public function tambahPengumuman()
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/login');
        
        $roles = session()->get('roles') ?? [];
        if (!in_array('admin', $roles) && !in_array('ketua', $roles)) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $this->pengumumanModel->save([
            'judul'      => $this->request->getPost('judul'),
            'isi'        => $this->request->getPost('isi'),
            'tanggal'    => date('Y-m-d H:i:s'), // Sekarang sudah WIB karena Config App diubah
            'created_by' => session()->get('nama')
        ]);

        return redirect()->to('/dashboard')->with('success', 'Pengumuman berhasil diterbitkan.');
    }

    // --- UPDATE (BARU) ---
    public function updatePengumuman()
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/login');

        $roles = session()->get('roles') ?? [];
        if (!in_array('admin', $roles) && !in_array('ketua', $roles)) {
            return redirect()->back();
        }

        $id = $this->request->getPost('id'); // Ambil ID hidden

        $this->pengumumanModel->update($id, [
            'judul' => $this->request->getPost('judul'),
            'isi'   => $this->request->getPost('isi'),
            // Tanggal tidak diupdate agar ketahuan kapan posting aslinya
            // Jika ingin tanggal berubah saat diedit, tambahkan field 'tanggal' disini
        ]);

        return redirect()->to('/dashboard')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    // --- HAPUS ---
    public function hapusPengumuman($id)
    {
        if (!session()->get('is_logged_in')) return redirect()->to('/login');
        
        $roles = session()->get('roles') ?? [];
        if (!in_array('admin', $roles) && !in_array('ketua', $roles)) {
            return redirect()->back();
        }

        $this->pengumumanModel->delete($id);
        return redirect()->to('/dashboard')->with('success', 'Pengumuman dihapus.');
    }
}