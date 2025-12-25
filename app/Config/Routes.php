<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');
$routes->get('/login', 'Auth::index');
$routes->post('/auth/process', 'Auth::process');
$routes->get('/logout', 'Auth::logout');

// Route Dashboard (Hanya bisa diakses setelah login - nanti kita pasang filter)
$routes->get('/dashboard', 'Dashboard::index');
