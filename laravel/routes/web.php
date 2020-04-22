<?php

use Illuminate\Support\Facades\Route;

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
    return redirect('/gateways');
});

Route::middleware(['auth.web'])->group(function () {
    Route::get('/gateways', 'GatewayController@showAllGatewaysPage');
    Route::get('/gateways/create', 'GatewayController@showGatewayActivationPage');
    Route::post('/gateways', 'GatewayController@activateGateway');
});

Route::get('/users', 'UserController@create');
Route::post('/users', 'UserController@store');

Route::get('/auth/login', 'AuthController@showLoginPage');
Route::post('/auth/login', 'AuthController@login');
Route::post('/auth/logout', 'AuthController@logout');
