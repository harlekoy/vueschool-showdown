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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('mock/individual', 'MockApiController@individual')
    ->name('mock.individual')
    ->middleware('throttle:50,60');

Route::post('mock/bulk', 'MockApiController@bulk')
    ->name('mock.bulk')
    ->middleware('throttle:3600,60');
