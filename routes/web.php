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

/** Login */
//Auth::routes();
Route::get('/oauth-callback', 'GoogleAuthController@handle')->name('oauth-callback');
Route::get('/login-google', 'GoogleAuthController@redirect')->name('login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

Route::group(['middleware' => 'auth'], function () {
    /** Home */
    Route::get('/', 'HomeController@index')
        ->name('home');

    /** Admin */
    Route::get('/admin', function () {
        return 'Admin Here';
    })->name('admin');

    /** Hours */
    Route::post('/hours/new', 'HoursController@store');
    Route::delete('/hours/delete', 'HoursController@delete');
    Route::get('/hours', function () {
        return 'My Hours';
    })->name('my-hours');
});

/** Import Test */
Route::get('/import', 'StudentInfoController@handleImport');