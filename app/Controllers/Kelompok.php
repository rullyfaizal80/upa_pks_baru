<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KelompokModel;
use App\Models\UserModel;
use App\Models\AnggotaKelompokModel;

class Kelompok extends BaseController
{
    protected $kelompokModel;
    protected $userModel;
    protected $anggotaModel;
    protected $db;

    public function __construct()
    {
        $this->kelompokModel = new KelompokModel();
        $this->userModel = new UserModel();
        $this->anggotaModel = new AnggotaKelompokModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'kelompok' => $this->kelompokModel->getKelompokLengkap()->paginate(10, 'kelompok'),
            'pager'    => $this->kelompokModel->pager
        ];
        return view('kelompok/index', $data);
    }

    // --- FORM CREATE (PEMBINA & SEKERTARIS DIPILIH DISINI) ---
    public function create()
    {
        // 1. Ambil list Pembina
        $pembinas = $this->getUsersByRole('pembina');
        
        // 2. Ambil list Sekertaris
        $sekertaris = $this->getUsersByRole('sekertaris');

        $data = [
            'pembinas' => $pembinas,
            'sekertaris' => $sekertaris
        ];
        return view('kelompok/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'nama_kelompok' => 'required',
            'pembina_id'    => 'required',
            'sekertaris_id' => 'required' // Wajib diisi sekarang
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kelompokModel->save([
            'nama_kelompok' => $this->request->getVar('nama_kelompok'),
            'pembina_id'    => $this->request->getVar('pembina_id'),
            'sekertaris_id' => $this->request->getVar('sekertaris_id')
        ]);

        return redirect()->to('/kelompok')->with('success', 'Kelompok berhasil dibuat dengan Pembina & Sekertaris.');
    }

    // --- MANAGE ANGGOTA (HANYA ROLE ANGGOTA) ---
    public function manage($id)
    {
        $kelompok = $this->kelompokModel->getKelompokLengkap()->find($id);
        if (!$kelompok) return redirect()->to('/kelompok');

        // 1. Ambil Anggota yang SUDAH masuk kelompok ini
        $members = $this->anggotaModel->getMembersByKelompok($id);

        // 2. Ambil Calon Anggota (Logic BARU)
        // Syarat: Role 'anggota' DAN ID-nya TIDAK ADA di tabel anggota_kelompok manapun
        
        $calonAnggota = $this->db->table('users')
             ->select('users.id, users.nama, users.jenjang')
             ->join('user_roles', 'users.id = user_roles.user_id')
             ->join('roles', 'roles.id = user_roles.role_id')
             ->where('roles.role_name', 'anggota')
             // Filter: Buang user yang sudah ada di tabel anggota_kelompok
             ->whereNotIn('users.id', function($builder) {
                 return $builder->select('user_id')->from('anggota_kelompok');
             })
             ->groupBy('users.id')
             ->orderBy('users.nama', 'ASC') // Urutkan nama abjad agar rapi
             ->get()->getResultArray();

        $data = [
            'kelompok' => $kelompok,
            'members'  => $members,
            'calonAnggota' => $calonAnggota
        ];

        return view('kelompok/manage', $data);
    }

    public function addMember()
    {
        $kelompokId = $this->request->getVar('kelompok_id');
        $userId     = $this->request->getVar('user_id');

        if (!$kelompokId || !$userId) {
            return redirect()->back()->with('errors', ['Pilih anggota terlebih dahulu.']);
        }

        // Cek apakah user sudah masuk kelompok manapun
        if ($this->anggotaModel->isUserInGroup($userId)) {
            return redirect()->back()->with('errors', ['User ini sudah terdaftar di kelompok lain/ini.']);
        }

        // Simpan (Tanpa kolom jabatan, karena ini pasti anggota biasa)
        $this->anggotaModel->insert([
            'kelompok_id' => $kelompokId,
            'user_id'     => $userId
        ]);

        return redirect()->back()->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function removeMember($id)
    {
        $this->anggotaModel->delete($id);
        return redirect()->back()->with('success', 'Anggota dikeluarkan.');
    }

    // Helper function untuk ambil user by role
    private function getUsersByRole($roleName) {
        return $this->db->table('users')
            ->select('users.id, users.nama')
            ->join('user_roles', 'users.id = user_roles.user_id')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('roles.role_name', $roleName)
            ->groupBy('users.id')
            ->get()->getResultArray();
    }

    // --- FORM EDIT KELOMPOK ---
    public function edit($id)
    {
        // 1. Ambil data kelompok berdasarkan ID
        $kelompok = $this->kelompokModel->find($id);
        if (!$kelompok) {
            return redirect()->to('/kelompok')->with('errors', ['Kelompok tidak ditemukan']);
        }

        // 2. Ambil list Pembina & Sekertaris (untuk Dropdown)
        $pembinas = $this->getUsersByRole('pembina');
        $sekertaris = $this->getUsersByRole('sekertaris');

        $data = [
            'kelompok' => $kelompok,
            'pembinas' => $pembinas,
            'sekertaris' => $sekertaris
        ];

        return view('kelompok/edit', $data);
    }

    // --- PROSES UPDATE KELOMPOK ---
    public function update($id)
    {
        // Validasi
        if (!$this->validate([
            'nama_kelompok' => 'required',
            'pembina_id'    => 'required',
            'sekertaris_id' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Simpan Perubahan
        $this->kelompokModel->update($id, [
            'nama_kelompok' => $this->request->getVar('nama_kelompok'),
            'pembina_id'    => $this->request->getVar('pembina_id'),
            'sekertaris_id' => $this->request->getVar('sekertaris_id')
        ]);

        return redirect()->to('/kelompok')->with('success', 'Data kelompok berhasil diperbarui.');
    }
}