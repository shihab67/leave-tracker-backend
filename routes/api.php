<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api', 'throttle:60,1')->group(function () {
    Route::get('stats', 'DashboardController@getStats')->name('dashboard.stats')->middleware('access:1,2,3');

    Route::get('users', 'UserController@getAllUsers')->name('user.all')->middleware('access:1,2');
    Route::get('user/{id}', 'UserController@getUser')->name('user')->middleware('access:1,2,3');
    Route::post('user/update-status', 'UserController@updateStatus')->name('user.update.status')->middleware('access:1,2');

    Route::group(['prefix' => 'leave-request'], function () {
        Route::post('/', 'LeaveRequestController@store')->name('leave.request.store');
        Route::post('/update', 'LeaveRequestController@update')->name('leave.request.update')->middleware('access:1,2');
        Route::get('/all/{type?}', 'LeaveRequestController@getLeaves')->name('leave.request.all')->middleware('access:1,2,3');
        Route::get('{id}', 'LeaveRequestController@getLeave')->name('leave.request.get')->middleware('access:1,2,3');
    });
});

Route::group(['middleware' => ['cors', 'json.response', 'throttle:60,1']], function () {
    Route::post('login', 'Auth\ApiAuthController@login')->name('login.api');
    Route::post('register', 'Auth\ApiAuthController@register')->name('register.api');
});
