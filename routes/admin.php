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
    Route::resource('/administrators', 'AdministratorController')->except(['create', 'edit', 'destroy']);
    Route::post('/administrators/reset-pwd', 'AdministratorController@resetPwd');
    Route::post('/administrators/ops', 'AdministratorController@ops');
    Route::post('/administrators/delete', 'AdministratorController@delete');

//    教师管理
    Route::get('/teachers/index', 'TeacherController@list');
    Route::resource('/teachers', 'TeacherController')->except(['create', 'edit', 'destroy']);
    Route::post('/teachers/ops', 'TeacherController@ops');
    Route::post('/teachers/reset-pwd', 'TeacherController@resetPwd');
    Route::post('/teachers/delete', 'TeacherController@delete');

//    学生管理
    Route::get('/students/index', 'StudentController@list');
    Route::resource('/students', 'StudentController')->except(['create', 'edit', 'destroy']);
    Route::post('/students/ops', 'StudentController@ops');
    Route::post('/students/reset-pwd', 'StudentController@resetPwd');
    Route::post('/students/delete', 'StudentController@delete');

//    分类管理
    Route::get('/topicCategories/index', 'TopicCategoryController@list');
    Route::resource('/topicCategories', 'TopicCategoryController')->except(['create', 'edit', 'destroy']);
    Route::post('/topicCategories/delete', 'TopicCategoryController@delete');

//    选题管理
    Route::get('/topics/index', 'TopicController@list');
    Route::resource('/topics', 'TopicController')->except(['create', 'edit', 'destroy']);
    Route::post('/topics/delete', 'TopicController@delete');
    Route::post('/topics/ops', 'TopicController@ops');

//    系别管理
    Route::get('/departments/index', 'DepartmentController@list');
    Route::resource('/departments', 'DepartmentController')->except(['create', 'edit', 'destroy']);
    Route::post('/departments/delete', 'DepartmentController@delete');

//    批量导入
    Route::get('/excel/import/logs', 'ImportErrorLogController@list');
    Route::post('/excel/import', 'ExcelController@import');
    Route::get('/excel/export', 'ExcelController@export');
    Route::post('/excel/import/logs/manage', 'ImportErrorLogController@manage');
});

Route::get('test', function () {
    return view('admin/test');
});


Route::get('/setting/time', function () {
    return view('admin/setting/time');
});

Route::get('/index_con', function () {
    return view('admin/index_con');
});
