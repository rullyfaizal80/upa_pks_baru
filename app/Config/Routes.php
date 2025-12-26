<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');
$routes->get('/login', 'Auth::index');
$routes->post('/auth/process', 'Auth::process');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Dashboard::index');

// --- MANAJEMEN USERS ---
$routes->get('users', 'Users::index');           // Menampilkan tabel user
$routes->get('users/create', 'Users::create');   // Form tambah user
$routes->post('users/store', 'Users::store');    // Proses simpan database
$routes->post('users/reset-password/(:num)', 'Users::resetPassword/$1');

$routes->get('users/edit/(:num)', 'Users::edit/$1');
$routes->post('users/update/(:num)', 'Users::update/$1');
$routes->post('users/delete/(:num)', 'Users::delete/$1'); // Pakai POST agar aman

// --- MANAJEMEN KELOMPOK ---
$routes->get('kelompok', 'Kelompok::index');
$routes->get('kelompok/create', 'Kelompok::create');
$routes->post('kelompok/store', 'Kelompok::store');

// ...
$routes->get('kelompok/manage/(:num)', 'Kelompok::manage/$1'); // Halaman Manage
$routes->post('kelompok/add-member', 'Kelompok::addMember');    // Proses Tambah
$routes->get('kelompok/remove-member/(:num)', 'Kelompok::removeMember/$1'); // Hapus Member
// ... Route kelompok sebelumnya ...
$routes->get('kelompok/edit/(:num)', 'Kelompok::edit/$1');      // Form Edit
$routes->post('kelompok/update/(:num)', 'Kelompok::update/$1'); // Proses Update

$routes->group('laporan', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'LaporanAmalanController::dashboard'); // Halaman List Laporan
    $routes->get('create', 'LaporanAmalanController::create');       // Form Isi Laporan
    $routes->post('store', 'LaporanAmalanController::store');        // Proses Simpan
    $routes->get('edit/(:num)', 'LaporanAmalanController::edit/$1'); // Form Edit
    $routes->post('update/(:num)', 'LaporanAmalanController::update/$1'); // Proses Update
    $routes->post('delete/(:num)', 'LaporanAmalanController::delete/$1');
});

// Routes Ganti Password (Universal untuk semua role)
$routes->get('ganti-password', 'Auth::gantiPassword');
$routes->post('ganti-password/update', 'Auth::updatePassword');
