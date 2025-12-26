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

        // Ambil Anggota yang sudah masuk
        $members = $this->anggotaModel->getMembersByKelompok($id);

        // Ambil calon Anggota (HANYA ROLE 'ANGGOTA')
        // Logic: Role harus 'anggota', DAN belum punya kelompok
        $calonAnggota = $this->db->table('users')
             ->select('users.id, users.nama, users.jenjang')
             ->join('user_roles', 'users.id = user_roles.user_id')
             ->join('roles', 'roles.id = user_roles.role_id')
             ->where('roles.role_name', 'anggota') // Filter keras hanya role anggota
             ->groupBy('users.id')
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
}