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

//Route::get('/login', 'LoginController@index');
Route::get('/login', 'LoginController@index');
Route::post('/login', 'LoginController@login');
Route::group(['middleware' => 'checkAdminLogin'], function () {
    Route::get('/home', 'WelcomeController@index');
});

Route::get('/setting/time', function () {
    return view('admin/setting/time');
});

Route::get('/index_con', function () {
    return view('admin/index_con');
});

Route::get('/admin_manage', function () {
    return view('admin/adminmanage/index');
});