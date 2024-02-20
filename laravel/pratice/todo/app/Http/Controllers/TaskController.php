<?php

namespace App\Http\Controllers;
use App\Task;
use App\TaskMapping;
use App\User;
// use App\Models\User;/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    //CRUD
    public function get_task(Request $request){
        return Task::get_task($request);
        // $titles = DB::table('users')->get();
        // // dd($titles);
        // return response()->json(['title' => $titles]);
    }
    public function get_specific_task(Request $request,$task_id){
        return Task::get_taskid($request,$task_id);
        // return "get $";
    }
    public function get_user_task(Request $request,$user_id){
        return TaskMapping::get_user_task($request,$user_id);
    }

    public function create_task(Request $request){
        return Task::create_task($request);
        // return "create";
    }
    public function assign_task(Request $request){
       return TaskMapping::create_task_map($request);
        // task_id
        // return "task assign";
    }
    public function edit_task(Request $request,$task_id){
        return Task::edit_task($request,$task_id);
        // return "edit";
    }
    public function edit_map_task(Request $request,$task_map_id){
        return TaskMapping::edit_map_task($request,$task_map_id);
        // return "status";
    }
    public function edit_map_status(Request $request,$task_map_id){
        return TaskMapping::edit_map_status($request,$task_map_id);
        // return "status";
    }
    public function edit_status_admin(Request $request,$task_id){
        return Task::edit_status($request,$task_id);
        // return "status admin";
    }
    public function delete_task(Request $request,$task_id){
        return Task::delete_task($request,$task_id);
        // return $task_id;
    }
    public function delete_map(Request $request,$task_map_id){
        return TaskMapping::delete_task_map($request,$task_map_id);
        // return "delete map";
    }

    
}
