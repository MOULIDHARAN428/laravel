<?php

namespace App\Http\Controllers;
use App\Task;
use App\TaskMapping;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{

    //CRUD
    public function get_task(){
        $task_count = Task::count();
        if($task_count==0){
            return response()->json([
                "ok" => true,
                "message" => "No tasks found"
            ], 200);
        }
        $tasks = Task::get_task();
        return response()->json([
            "ok" => true,
            "tasks" => $tasks
        ], 200);
    }
    public function get_specific_task($task_id){
        $task_present_or_not = Task::find($task_id);
        if(!$task_present_or_not){
            return response()->json([
                "ok" => false,
                "message" => "Task ID is not available"
            ], 404);
        }
        $task = Task::get_taskid($task_id);
        return response()->json([
            "ok" => true,
            "task" => $task          
        ], 200);
    }
    public function get_user_task($user_id){
        $is_user_assigned_task = TaskMapping::where('user_id',$user_id)->first();
        if(!isset($is_user_assigned_task)){
            return response()->json([
                "ok" => false,
                "message" => "User hasn't assigned any task yet!"
            ], 404);
        }
        $tasks = TaskMapping::get_user_task($user_id);
        return response()->json([
            "ok" => true,
            "tasks" => $tasks            
        ], 200);
    }

    public function create_task(Request $request){
        if(!isset($request['title'])){
            return response()->json([
                "ok" => false,
                "message" => "Title is Required!"
            ], 200);
        }
        if(!isset($request['description'])){
            return response()->json([
                "ok" => false,
                "message" => "Description is Required!"
            ], 200);
        }
        if(isset($request['parent_id']) && !Task::where('id', $request['parent_id'])->exists()){
            return response()->json([
                "ok" => false,
                "message" => "No such parent task with ID ".$request['parent_id']." found!"
            ], 200);

        }

        $task = Task::create_task($request);
        return response()->json([
            "ok" => true,
            "task" => $task,
            "message" => "Task is created"
        ], 200);
    }

    public function assign_task(Request $request){
        if(!isset($request['task_id'])){
            return response()->json([
                "ok" => false,
                "message" => "Task ID is Required!"
            ], 200);
        }
        if(!isset($request['user_id'])){
            return response()->json([
                "ok" => false,
                "message" => "User ID is Required!"
            ], 200);
        }
        if(!isset($request['role'])){
            return response()->json([
                "ok" => false,
                "message" => "Role is Required!"
            ], 200);
        }
        if(!isset($request['assigned_at'])){
            return response()->json([
                "ok" => false,
                "message" => "Assign Time is Required!"
            ], 200);
        }
        
        $task = Task::where('id',($request['task_id']))->first();
        if(!isset($task)){
            $message = 'No task with '.$request['task_id'].' is not found!';
            return response()->json([
                "ok" => false,
                "message" => $message
            ], 200);
        }

        $user = User::where('id',($request['user_id']))->first();
        if(!isset($user)){
            $message= 'No user with '.$request['user_id'].' is not found!';
            return response()->json([
                "ok" => false,
                "message" => $message
            ], 200);
        }

       $task_map = TaskMapping::create_task_map($request);
       return response()->json([
            "ok" => true,
            "task_map" => $task_map, 
            "message" => "Task is assigned!"
        ], 200); 
    }

    public function edit_task(Request $request,$task_id){
        if(!Task::find($task_id)){
            return response()->json([
                "ok" => true,
                "message" => "No Task with ID ".$task_id." not found",
            ], 400);
        }
        $task = Task::edit_task($request,$task_id);
        return response()->json([
            "ok" => true,
            "task" => $task,
            "message" => "Task is edited!"
        ], 200);
    }

    public function edit_map_task(Request $request,$task_map_id){
        if(!TaskMapping::find($task_map_id)){
            return response()->json([
                "ok" => false,
                "message" => "No such task map ID is found!",
            ], 400);
        }

        $status = TaskMapping::where('id',$task_map_id)->pluck('status');
        if($status){
            return response()->json([
                "ok" => false,
                "message" => "User task is finished!",
            ], 400);
        }

        if(isset($request['task_id'])){
            //checking whether the task id is present in database
            $task = Task::where('id',($request['task_id']))->first();
            if(!isset($task)){
                return response()->json([
                    "ok" => false,
                    "message" => "No such task ID is found",
                ], 400);
            }
        }

        if(isset($request['user_id'])){
            //check whether the user_id available
            $user = User::where('id',($request['user_id']))->first();
            if(!isset($user)){
                return response()->json([
                    "ok" => false,
                    "message" => "No such user is found",
                ], 400);
            }
        }

        $task_map = TaskMapping::edit_map_task($request,$task_map_id);
        return response()->json([
            "ok" => true,
            "task_map" => $task_map,
            "message" => "Task map is edited",
        ], 200);
        // return "status";
    }

    public function edit_map_status(Request $request,$task_map_id){
        if(!TaskMapping::find($task_map_id)){
            return response()->json([
                "ok" => false,
                "message" => "No such task map ID is found!"
            ], 400);
        }
        $user = Auth::user();
        $userID = $user->id;
        
        $check_task_exits = TaskMapping::where('user_id', $userID)
                ->where('task_id',$task_map_id)->first();
        
        if(!isset($check_task_exits)){
            return response()->json([
                "ok" => false,
                "message" => "No task matches"
            ], 400);
        }

        $task_map = TaskMapping::edit_map_status($request['status'],$task_map_id);
        return response()->json([
            "ok" => true,
            "task_map" => $task_map,
            "message" => "Task is edited!"
        ], 200);
        // return "status";
    }

    public function edit_status_admin(Request $request,$task_id){
        if(!isset($request['status'])){
            return response()->json([
                "ok" => true,
                "message" => "Status is Required!",
            ], 400);
        }

        if(!Task::find($task_id)){
            return response()->json([
                "ok" => true,
                "message" => "No such task ID is found!",
            ], 400);
        }

        $task = Task::edit_status($request['status'],$task_id);
        return response()->json([
            "ok" => true,
            "task" => $task,
            "message" => "Task status is updated"            
        ], 200);
    }

    public function delete_task($task_id){
        if(!Task::find($task_id)){
            return response()->json([
                "ok" => false,
                "message" => "No such task ID is found!",
            ], 400);
        }

        $message = Task::delete_task($task_id);
        return response()->json([
            "ok" => true,
            "message" => $message
        ], 200);
    }

    public function delete_map($task_map_id){
        if(!TaskMapping::find($task_map_id)){
            return response()->json([
                "ok" => false,
                "message" => "No such task map ID is found!",
            ], 400);
        }
        $message = TaskMapping::delete_task_map($task_map_id);
        return response()->json([
            "ok" => true,
            "message" => $message
        ], 200);
    }

    
}
