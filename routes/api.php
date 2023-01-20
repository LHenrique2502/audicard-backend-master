<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Debugbar::disable();

Route::namespace('Auth')->group(function () {
    Route::prefix('auth')->group(function () {

        Route::post('login', 'AuthController@authenticate')->name('authenticate');
        Route::get('refresh', 'AuthController@refresh');
        Route::get('me', 'AuthController@me');
        Route::post('logout', 'AuthController@logout');
    });
});


Route::namespace('Admin')->group(function () {

    Route::get('report/clients','ReportController@clients');
    Route::post('report/solicitation','ReportController@solicitation');

    Route::apiResources([
        'user' => 'UserController',
        'solicitation' => 'SolicitationController'
    ]);




});




