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

Route::get('/plain',function(){
    return "Please Log in!";
})->name('plain');

Route::get('/user', function (Request $request) {
    return "Logged In";
})->middleware('auth:api');

// Route::get('/admin',function(){
//     return "admin...";
// })->middleware('admin');

Route::get('/summa',function(Request $request){
    return "Heree";
});

Route::post('/register', 'Auth\RegisterController@registerPassport'); //done
Route::post('/login',  'Auth\LoginController@loginPassport')->name('login'); //done
Route::post('/logout', 'Auth\LoginController@logoutPassport')->middleware('auth:api'); //done

// forgot password and reset password
Route::post('/forgot-password', 'Auth\ForgotPasswordController@sendResetLinkEmailPassport');
Route::post('/reset-password','Auth\ResetPasswordController@resetPasswordPassport'); 

Route::post('/sort_by_time','TaskController@sortByTime');
// Route::get('/profile/{user_id}','TaskController@getUserAnalytics');

Route::get('/task_subtask_user','TaskController@getTasksWithSubTasks');


//Testing analytical api
Route::get('/analytics_day_data','TaskAnalytics@analyticsDayData');
Route::get('/analytics_hour_data','TaskAnalytics@analyticsHourData');
Route::get('/analytics_task_assignes','TaskAnalytics@analyticsTaskAssignes');
Route::get('/analytics_user_tasks','TaskAnalytics@analyticUserTasks');

Route::prefix('tasks')->group(function () {
    // GET
    Route::get('/','TaskController@getTasks'); 

    Route::get('/{task_id}','TaskController@getSpecificTask'); 

    Route::get('/mapping/{user_id}','TaskController@getUserTasks'); // need to test

    Route::middleware('auth:api')->group(function () {
        // POST
        Route::post('/','TaskController@createTask'); 

        Route::post('/mapping/status/{task_map_id}','TaskController@editMapStatus'); 

        
        Route::middleware('admin')->group(function () {

            Route::post('/mapping','TaskController@assignTask'); 

            Route::post('/mapping/{task_map_id}','TaskController@editMapTask'); 

            Route::post('/status/{task_id}','TaskController@editStatusAdmin');

            // DELETE
            Route::delete('/{task_id}','TaskController@deleteTask'); 

            Route::delete('/mapping/{task_map_id}','TaskController@deleteMap');
        
        });

        Route::post('/{task_id}','TaskController@editTask'); 

    });

    Route::fallback(function () {
        return "No such routers";
    });

});

Route::post('/upload-profile-picture', 'UserController@uploadProfilePicture')->middleware('auth:api');

Route::fallback(function () {
    return "No such routers";
});