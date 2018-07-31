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

/** Home */
Route::get('/', 'HomeController@index')
    ->name('home');

/** Admin */
Route::get('/admin', function () {
    return 'Admin Here';
})->name('admin');

/** Login */
//Auth::routes();
Route::get('/oauth-callback', 'GoogleAuthController@handle')->name('oauth-callback');
Route::get('/login-google', 'GoogleAuthController@redirect')->name('login');

/** Hours */
Route::post('/hours/new','HoursController@store');
Route::get('/hours', function() {
    return 'My Hours';
})->name('my-hours');

/** Import Test */
Route::get('/import', 'StudentInfoController@handleImport');