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

// testing
Route::get('/testimg','LiteBrite@start_php');

// Litebrite front-end pages
Route::get('/','LiteBrite@index');
Route::get('/submissions','LiteBrite@entries');
Route::get('/submissions/{id}','LiteBrite@show');

// LiteBrite front-end setters
Route::post('/store','LiteBrite@store');
Route::post('/update/{id}','LiteBrite@update');
Route::post('/upload','LiteBrite@upload');
Route::post('/delete/{id}','LiteBrite@destroy');

// lb admin pages
Route::get('/config','LiteBriteConfig@index');
Route::get('/config/{id}','LiteBriteConfig@show');
Route::get('/config/all','LiteBriteConfig@all');

// lb config setters
Route::post('/config/store','LiteBriteConfig@store');
Route::post('/config/store/{id}','LiteBriteConfig@update');
Route::post('/config/delete/{id}','LiteBriteConfig@destroy');

