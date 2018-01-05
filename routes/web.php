<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/', 'LoginController@index');
Route::post('login', 'LoginController@login');
Route::get('app/logout', 'LoginController@logout');
Route::post('app/user', 'DashboardController@user');
Route::post('app/orders', 'OrderController@orderList');
Route::post('app/user-edit', 'ProfileController@edit');

/*test routes*/
Route::get('/test',['as' => 'test', 'uses' => 'OrderController@findorder']);
Route::get('/test/{crm?}',['as' => 'test', 'uses' => 'test@index']); 

Route::post('/check_user', 'LoginController@checkUser');
Route::post('/check_otp', 'LoginController@checkOtp');
Route::post('/change_password', 'LoginController@changePassword');
Route::post('/basic-login' , 'LoginController@basicLogin');