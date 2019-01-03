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

// Getters
Route::get('/','liteBrite@index');
Route::get('/config','liteBrite@getConfig');
Route::get('/entries','liteBrite@entries');

// Setters
Route::post('/config','liteBrite@setConfig');
Route::post('/store','liteBrite@store');
Route::post('/upload','liteBrite@upload');