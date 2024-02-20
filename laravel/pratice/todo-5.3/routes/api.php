<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
// use App\Http\Controllers\UserController;


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

Route::group(['middleware' => ['cors', 'json.response']], function () {
    // Resit or to permit the entry from various sites
});


Route::get('/user', function (Request $request) {
    return "Logged In";
})->middleware('auth:api');

Route::post('/register', 'Auth\RegisterController@register'); //done
Route::post('/login', 'Auth\LoginController@login'); 
Route::post('/logout', 'Auth\LoginController@logout')->middleware('auth:api');
Route::post('/forgot-password', 'Auth\ForgotPasswordController@sendResetLinkEmail')->middleware('auth:api');
// Route::post('/upload-profile-picture', 'UserController@uploadProfilePicture')->middleware('auth:api');


?>