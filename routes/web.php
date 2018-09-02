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
Route::get('/logout', 'SessionController@logout')->name('logout');

/** Club Select */
Route::get('/clubs', 'ClubController@index')->name('club-select');
Route::get('/sessionswitch/{club}', 'SessionController@switchClub')->name('switch-club');
Route::post('/clubs/join', 'ClubController@join')->name('join');

Route::group(['middleware' => ['auth:user,admin', 'club']], function () {
    /**
     * Home
     */
    Route::get('/', 'HomeController@index')
        ->name('home');
    Route::post('/user/ajax/getInfo', 'UserController@getInfo')
        ->middleware('admin');

    /**
     * Hours
     */

    //Clock in, Clock Out
    Route::post('/hours/new', 'HoursController@store')->name('clock-in');
    Route::post('/hours/clockout/{hour}', 'HoursController@clockout')
        ->middleware('can:update,hour')
        ->name('clock-out');

    //Delete Timepunch
    Route::delete('/hours/delete/{hour}', 'HoursController@delete')
        ->middleware('can:delete,hour')
        ->name('delete-hour');

    //View Hours
    Route::get('/hours/{user?}', 'HoursController@index')->name('my-hours');
    Route::get('/hours/charts/{user}', 'HoursController@charts');
    Route::post('/hours/mark/{hour}', 'HoursController@mark')
        ->middleware('can:update,hour');
    Route::post('/hours/undoMark', 'HoursController@undoMark');
    Route::post('/hours/getData', 'HoursController@getData')
        ->middleware('admin');

    /**
     * Admin
     */
    Route::group(['middleware' => 'admin', 'prefix' => '/admin'], function () {
        //Home and Pages
        Route::get('/{page?}/{param?}', 'AdminController@index')
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
        Route::post('/hour/undoMark', 'AdminController@undoMark');
        Route::post('/hour/update', 'AdminController@updateHour');
        Route::get('/ajax/hours/statCharts', 'AdminController@charts');

        Route::put('/events/changeOrder', 'AdminController@changeEventsOrder');
        Route::put('/events/updateName', 'AdminController@updateEventName');
        Route::post('/events/toggleVis', 'AdminController@toggleVisibility');
        Route::delete('/events/delete', 'AdminController@deleteEvent');
        Route::post('/events/purge', 'AdminController@purgeEvent');
        Route::post('/events/create', 'AdminController@createEvent');

        Route::put('/club/update', 'ClubController@update')->name('update-club');
    });
});

/** Import Test */
Route::get('/import', 'StudentInfoController@handleImport');