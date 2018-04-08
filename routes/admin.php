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
    Route::get('/logout', 'LoginController@logout');
//    管理员管理
    Route::get('/administrators/index', 'AdministratorController@list');
    Route::resource('/administrators', 'AdministratorController');
    Route::match(['get', 'post'], '/administrators/reset-pwd/{id}', 'AdministratorController@resetPwd');
    Route::post('/administrators/ops', 'AdministratorController@ops');

//    教师管理
    Route::get('/teachers/index', 'TeacherController@list');
    Route::resource('/teachers', 'TeacherController');
    Route::post('/teachers/ops', 'TeacherController@ops');
    Route::get('/students/index', 'StudentController@list');
    Route::resource('/students', 'StudentController');
    Route::post('/students/ops', 'StudentController@ops');
});

Route::get('test', function () {
    return view('admin/test');
});
Route::get('/excel/import/logs', 'ImportErrorLogController@list');
Route::post('/excel/import', 'ExcelController@import');
Route::get('/excel/export', 'ExcelController@export');

Route::get('/setting/time', function () {
    return view('admin/setting/time');
});

Route::get('/index_con', function () {
    return view('admin/index_con');
});
