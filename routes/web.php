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
    Route::post('/hours/new', 'HoursController@store')->name('clock-out');
    Route::post('/hours/clockin/{hour}', 'HoursController@clockin')
        ->middleware('can:update,hour')
        ->name('clock-in');
    Route::delete('/hours/delete/{hour}', 'HoursController@delete')
        ->middleware('can:delete,hour')
        ->name('delete-hour');

    Route::get('/hours/{user?}', 'HoursController@index')->name('my-hours');
    Route::get('/hours/charts/{user}', 'HoursController@charts');

    Route::get('/hours/mark/{hour}', function (App\Hour $hour) {
        return 'My Hours & Mark For Review (Hour ID ' . $hour->id . ')';
    })->name('hour-mark');
});

/** Import Test */
Route::get('/import', 'StudentInfoController@handleImport');