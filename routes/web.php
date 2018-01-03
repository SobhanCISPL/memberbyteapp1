<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/', 'LoginController@index');

Route::post('login', 'LoginController@login');

Route::get('logout', 'LoginController@logout');

Route::get('/test',['as' => 'test', 'uses' => 'OrderController@findorder']);
Route::get('findorder', 'OrderController@orderList');
Route::get('/test/{crm?}',['as' => 'test', 'uses' => 'test@index']);
Route::post('/app/orders', 'OrderController@orderList'); 

Route::post('/check_user', 'LoginController@checkUser');
Route::post('/check_otp', 'LoginController@checkOtp');
Route::post('/change_password', 'LoginController@changePassword');
Route::post('/basic-login' , 'LoginController@basic_login');
// Route::get('/app/', 'DashboardController@index');