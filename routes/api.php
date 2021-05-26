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
Route::get('/ping', function () {
    return ['pong' => true];
});
Route::get('/401', 'App\Http\Controllers\AuthController@unauthorized')->name('login');

Route::post('/auth/login','App\Http\Controllers\AuthController@login');
Route::post('/auth/logout','App\Http\Controllers\AuthController@logout');
Route::post('/auth/refresh','App\Http\Controllers\AuthController@refresh');

Route::post('/user', 'App\Http\Controllers\AuthController@create');
Route::put('/user', 'App\Http\Controllers\UserController@update');
// Route::post('/user/avatar','App\Http\Controllers\UserController@updateAvatar');
// Route::post('/user/cover','App\Http\Controllers\UserController@updateCover');

// Route::get('/feed','App\Http\Controllers\FeedController@read');
// Route::get('/user/feed', 'App\Http\Controllers\FeedController@userFeed');
// Route::get('/user/{id}/feed','App\Http\Controllers\FeedController@userFeed');

// Route::get('/user','App\Http\Controllers\UserController@read');
// Route::get('/user/{id}','App\Http\Controllers\UserController@read');

// Route::post('/feed', 'App\Http\Controllers\FeedController@create');

// Route::post('/post/{id}/like', 'App\Http\Controllers\PostController@like');
// Route::post('/post/{id}/comment', 'App\Http\Controllers\PostController@comment');

// Route::post('/search','App\Http\Controllers\SearchController@search');
