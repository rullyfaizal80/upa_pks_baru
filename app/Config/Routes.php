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
