<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

// Auth routes
$routes->get('/', 'Home::index');
$routes->get('auth/login', 'Auth::login');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/register', 'Auth::register');
$routes->post('auth/register', 'Auth::register');
$routes->get('auth/logout', 'Auth::logout');

// Admin routes
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('notes', 'Admin::notes');
    $routes->get('deleteUser/(:num)', 'Admin::deleteUser/$1');
    $routes->get('deleteNote/(:num)', 'Admin::deleteNote/$1');
});

// Notes routes
$routes->group('notes', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Notes::index');
    $routes->get('create', 'Notes::create');
    $routes->post('create', 'Notes::create');
    $routes->get('edit/(:num)', 'Notes::edit/$1');
    $routes->post('edit/(:num)', 'Notes::edit/$1');
    $routes->get('delete/(:num)', 'Notes::delete/$1');
});

// Comment routes
$routes->post('notes/(:num)/comment', 'Notes::addComment/$1');
$routes->get('notes/(:num)/comments', 'Notes::getComments/$1');

// Notes routes
$routes->get('notes', 'Notes::index');
$routes->get('notes/create', 'Notes::create');
$routes->post('notes/store', 'Notes::store');
$routes->get('notes/edit/(:num)', 'Notes::edit/$1');
$routes->post('notes/update/(:num)', 'Notes::update/$1');
$routes->get('notes/delete/(:num)', 'Notes::delete/$1');
$routes->post('notes/addComment', 'Notes::addComment');
$routes->get('notes/getComments/(:num)', 'Notes::getComments/$1');
$routes->get('notes/testMongo', 'Notes::testMongo');

return $routes;
