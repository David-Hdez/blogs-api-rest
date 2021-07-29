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
// User
Route::post('/user', 'UserController@store');
Route::post('/user/login', 'UserController@login');
Route::put('/user', 'UserController@update');
Route::post('/user/avatar', 'UserController@storeAvatar')->middleware('jwt');
Route::get('/user/avatar/{image}', 'UserController@showAvatar');
Route::get('/user/{id}', 'UserController@show');
// Category
Route::resource('category', 'CategoryController');
// Post
Route::resource('post', 'PostController');
