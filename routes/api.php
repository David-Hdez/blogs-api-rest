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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/user/store', 'UserController@store');
Route::post('/user/login', 'UserController@login');
Route::put('/user/update', 'UserController@update');
Route::post('/user/avatar/upload', 'UserController@avatarUpload')->middleware('jwt');
