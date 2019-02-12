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

// Litebrite front-end pages
Route::get('/', 'LiteBrite@index');
Route::get('/submissions', 'LiteBrite@entries');
Route::get('/submissions/{id}', 'LiteBrite@show');
Route::post('/get-image', 'LiteBrite@getImage');

// LiteBrite front-end setters
Route::post('/store', 'LiteBrite@store');
Route::post('/update/{id}', 'LiteBrite@update');
Route::post('/uploader', 'LiteBrite@upload');
Route::post('/delete/{id}', 'LiteBrite@destroy');

// lb admin pages
Route::get('/config', 'LiteBriteConfigController@index');
Route::get('/config/{id}', 'LiteBriteConfigController@index');
Route::get('/config/all', 'LiteBriteConfigController@all');

// lb config setters
Route::get('/active-config', 'LiteBriteConfigController@getActiveConfig');
Route::post('/get-config', 'LiteBriteConfigController@getConfig');
Route::post('/config/store', 'LiteBriteConfigController@store');
Route::post('/config/store/{id}', 'LiteBriteConfigController@update');
Route::post('/config/delete/{id}', 'LiteBriteConfigController@destroy');

