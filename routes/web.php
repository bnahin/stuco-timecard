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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/oauth-callback','GoogleAuthController@handle')->name('oauth-callback');
Route::get('/login-google','GoogleAuthController@redirect')->name('oauth-redirect');

Route::get('/home', 'HomeController@index')->name('home');
