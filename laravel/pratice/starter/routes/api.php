<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
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

Route::post('/register', 'Auth\RegisterController@register');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout')->middleware('auth:api');
Route::post('/forgot-password', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/upload-profile-picture', 'UserController@uploadProfilePicture')->middleware('auth:api');