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

Route::get('/','liteBrite@index');
Route::get('/config','liteBrite@getConfig');
Route::post('/config','liteBrite@setConfig');
Route::post('/store','liteBrite@store');