<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/
/*Before login*/
Route::get('/', 'LoginController@index');
Route::post('login', 'LoginController@login');
Route::post('/check-user', 'LoginController@checkUser');
Route::post('/check-otp', 'LoginController@checkOtp');
Route::post('/change-password', 'LoginController@changePassword');
Route::post('/basic-login' , 'LoginController@basicLogin');

/*After login*/
Route::get('app/logout', 'LoginController@logout');
Route::post('app/user', 'DashboardController@user');
Route::post('app/orders', 'OrderController@orderList');
Route::post('app/user-edit', 'ProfileController@edit');

/*201clicks data related*/
Route::post('app/api-data' , 'ApiController@sessionStor');
Route::post('app/order-options' , 'OrderController@orderOptions');

/*test routes*/
Route::get('/test',['as' => 'test', 'uses' => 'OrderController@findorder']);
Route::get('/test/{crm?}',['as' => 'test', 'uses' => 'test@index']);