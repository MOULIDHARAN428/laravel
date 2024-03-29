<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::get('/task','TaskViewController@tasks');

    Route::get('/profile','TaskViewController@profile');
    
    Route::get('/task_subtask_user','TaskController@getTasksWithSubTasks');
    
    Route::get('/users_with_profile','TaskController@getUsersWithProfile');
    
    Route::get('/task_with_user/{task_id}','TaskController@getTaskWithUsers');
    
    Route::get('/task/{task_id}','TaskViewController@specificTask');
    
    Route::get('/parent-task/{task_id}','TaskViewController@specificParentTask');
    
    Route::get('/profile-analytics','TaskController@getUserAnalytics');
    
    Route::get('/task_with_subtask_with_user/{task_id}','TaskController@getTaskWithSubtask');

    Route::post('/sort_by_time','TaskController@sortByTime');

    Route::post('/import_excel','TaskController@importExcel');

    Route::get('/analytics_day_data','TaskAnalytics@analyticsDayData');

    Route::get('/analytics_hour_data','TaskAnalytics@analyticsHourData');
    
    Route::get('/analytics_task_assignes','TaskAnalytics@analyticsTaskAssignes');
    
    Route::get('/analytics_user_tasks','TaskAnalytics@analyticUserTasks');

});
// routes for getting the data
Route::prefix('tasks')->group(function () {
    // GET
    Route::get('/','TaskController@getTasks'); 

    // Route::get('/{task_id}','TaskController@getSpecificTask'); 

    Route::get('/mapping/{user_id}','TaskController@getUserTasks'); // need to test

    Route::middleware(['auth'])->group(function () {
        // POST
        Route::post('/','TaskController@createTask'); 

        Route::post('/mapping/status/{task_map_id}','TaskController@editMapStatus'); 

        Route::post('/mapping','TaskController@assignTask'); 

        Route::post('/mapping/{task_map_id}','TaskController@editMapTask'); 

        Route::post('/status/{task_id}','TaskController@editStatusAdmin');

        // DELETE
        Route::delete('/{task_id}','TaskController@deleteTask'); 

        Route::delete('/mapping/{task_map_id}','TaskController@deleteMap');
        

        Route::post('/{task_id}','TaskController@editTask'); 

    });

    Route::fallback(function () {
        return "No such routers";
    });

});

Route::get('/home', 'HomeController@index')->name('home');
