<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/index', function () {
    return view('admin/index');
});

Route::get('/admin/index_con', function () {
    return view('admin/index_con');
});

Route::get('/admin/admin_manage', function () {
    return view('admin/adminmanage/index');
});