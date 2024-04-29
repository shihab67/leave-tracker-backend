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
    Route::post('user/update-status', 'UserController@updateStatus')->name('user.update.status')->middleware('access:1,2');

    Route::group(['prefix' => 'leave-request'], function () {
        Route::post('/', 'LeaveRequestController@store')->name('leave.request.store');
        Route::post('/approve', 'LeaveRequestController@approve')->name('leave.request.approve')->middleware('access:1,2');
    });
});

Route::group(['middleware' => ['cors', 'json.response', 'throttle:60,1']], function () {
    Route::post('login', 'Auth\ApiAuthController@login')->name('login.api');
    Route::post('register', 'Auth\ApiAuthController@register')->name('register.api');
});
