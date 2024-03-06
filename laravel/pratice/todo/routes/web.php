<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!

*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register',function(){
    return view('auth.register');
});
// I have register.blade.php file in auth folder inside the views. Why the above line throws me an error "Route [your.register.route] not defined. (View: /home/mouli/Desktop/Practice/laravel/pratice/todo/resources/views/auth/register.blade.php)"

Route::get('/login',function(){
    return view('auth.login');
});
Route::get('/logout',function(){
    return view('auth.logout');
});
Route::get('/forgot-password',function(){
    return view('auth.forgotPassword');
});
Route::get('/reset-password',function(){
    return view('auth.resetPassword');
});

Route::fallback(function () {
    return "No such routers";
});