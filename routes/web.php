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
Route::get('/login-google', 'GoogleAuthController@redirect')->name('login');
Route::get('/oauth-callback', 'GoogleAuthController@handle')->name('oauth-callback');

/** Logout */
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

Route::group(['middleware' => 'auth:user,admin'], function () {
    /**
     * Home
     */
    Route::get('/', 'HomeController@index')
        ->name('home');

    /**
     * Hours
     */

    //Clock in, Clock Out
    Route::post('/hours/new', 'HoursController@store')->name('clock-out');
    Route::post('/hours/clockin/{hour}', 'HoursController@clockin')
        ->middleware('can:update,hour')
        ->name('clock-in');
    //Delete Timepunch
    Route::delete('/hours/delete/{hour}', 'HoursController@delete')
        ->middleware('can:delete,hour')
        ->name('delete-hour');

    //View Hours
    Route::get('/hours/{user?}', 'HoursController@index')->name('my-hours');
    Route::get('/hours/charts/{user}', 'HoursController@charts');
    Route::get('/hours/mark/{hour}', function (App\Hour $hour) {
        return 'My Hours & Mark For Review (Hour ID ' . $hour->id . ')';
    })->name('hour-mark');

    /**
     * Admin
     */
    Route::group(['middleware' => 'admin', 'prefix' => '/admin'], function () {
        //Home and Pages
        Route::get('/{page?}', 'AdminController@index')
            ->name('admin');

        //Submissions/AJAX
        Route::post('/enrolled/get', 'AdminController@processEnrolledStudentsTable')
            ->name('get-enrolled');
        Route::post('/assign', 'AdminController@assignStudent')
            ->name('manual-assign');
        Route::put('/unblock', 'AdminController@unblock');
        Route::put('/students/drop', 'AdminController@dropStudent');
        Route::post('/students/purge', 'AdminController@purgeStudents');
        Route::post('/hour/getdata', 'AdminController@getHourData');
    });
});

/** Import Test */
Route::get('/import', 'StudentInfoController@handleImport');