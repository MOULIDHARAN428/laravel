<?php

namespace App\Http\Controllers;
use App\Http\Resources\TaskResponseResource;
use App\Task;
use App\TaskMapping;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{

    //CRUD
    public function getTasks(){
        $validator = Validator::make([], [
            'tasks_exist' => 'exists:tasks',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error_message = implode(' ', $errors);
            return response()->json([
                'ok' => false,
                'error' => $error_message
            ],404);  
        }

        $tasks = Task::getTasks();
        $task_data = new TaskResponseResource($tasks);
        return response()->json([
            'ok' => true,
            'task' => $task_data
        ], 200);

    }
    public function getSpecificTask($task_id){
        $validator = Validator::make(['task_id' => $task_id], [
            'task_id' => 'exists:tasks,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error_message = implode(' ', $errors);
            return response()->json([
                'ok' => false,
                'error' => $error_message
            ],404);  
        }

        $task = Task::getTaskID($task_id);
        $task_data = new TaskResponseResource($task);
        return response()->json([
            'ok' => true,
            'task' => $task_data
        ], 200);
    }
    public function getUserTask($user_id){
        $validator = Validator::make(['task_id' => $user_id], [
            'user_id' => 'exists:task_mappings,user_id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error_message = implode(' ', $errors);
            return response()->json([
                'ok' => false,
                'error' => $error_message
            ],404);  
        }

        $tasks = TaskMapping::getUserTask($user_id);
        $task_data = new TaskResponseResource($tasks);
        return response()->json([
            'ok' => true,
            'task' => $task_data
        ], 200);
    }

    public function createTask(Request $request){

        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'description' => 'required',
            'due_time'=> 'required',
            'parent_id'=> 'exists:tasks,id'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error_message = implode(' ', $errors);
            return response()->json([
                'ok' => false,
                'error' => $error_message
            ],404);  
        }

        $task = Task::createTask($request);
        $task_data = new TaskResponseResource($task);
        return response()->json([
            'ok' => true,
            'task' => $task_data
        ], 200);
    }

    public function assignTask(Request $request){
        $validator = Validator::make($request->all(),[
            'task_id' => 'required|exists:tasks,id',
            'user_id' => 'required|exists:users,id',
            'role'=> 'required',
            'assigned_at'=> 'required',
            'parent_id'=> 'exists:tasks,id'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error_message = implode(' ', $errors);
            return response()->json([
                'ok' => false,
                'error' => $error_message
            ],404);  
        }

        $task_map = TaskMapping::createTaskMap($request);
        $task_map_data = new TaskResponseResource($task_map);
        return response()->json([
            'ok' => true,
            'task' => $task_map_data
        ], 200);
    }

    public function editTask(Request $request,$task_id){
        $validator = Validator::make(['task_id' => $task_id], [
            'task_id' => 'required|exists:tasks,id',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error_message = implode(' ', $errors);
            return response()->json([
                'ok' => false,
                'error' => $error_message
            ],404);  
        }
        
        $task_map = Task::editTask($request,$task_id);
        $task_map_data = new TaskResponseResource($task_map);
        return response()->json([
            'ok' => true,
            'task' => $task_map_data
        ], 200);
    }

    public function editMapTask(Request $request,$task_map_id){
        $request['task_map_id'] = $task_map_id;
        $validator = Validator::make($request->all(),[
            'task_id' => 'exists:tasks,id',
            'user_id' => 'exists:users,id',
            'role'=> 'required',
            'assigned_at'=> 'required',
            'parent_id'=> 'exists:tasks,id',
            'task_map_id' => ['required|exists:task_mappings,id',
                                Rule::exists('task_mappings', 'id')->where(function ($query) {
                                    $query->where('status', '!=', 1);
                                })],

        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error_message = implode(' ', $errors);
            return response()->json([
                'ok' => false,
                'error' => $error_message
            ],404);  
        }

        //users need to be edited alone for analytical purposes
        // if(isset($request['user_id']) && (isset($request['task_id']) || isset($request['role']) || 
        // isset($request['assigned_at']) || isset($request['status']))){
        //     return response()->json([
        //         "ok" => false,
        //         "message" => "user id and other map details should be edited separately",
        //     ], 400);
        // }

        $task_map = TaskMapping::editMapTask($request,$task_map_id);
        $task_map_data = new TaskResponseResource($task_map);
        return response()->json([
            'ok' => true,
            'task' => $task_map_data
        ], 200);
    }

    public function editMapStatus(Request $request,$task_map_id){
        $request['task_map_id'] = $task_map_id;
        $user = Auth::user();
        $userID = $user->id;
        $validator = Validator::make($request->all(),[
            'user_id' => 'exists:users,id',
            'task_map_id' => ['required|exists:task_mappings,id',
                            Rule::exists('task_mappings', 'id')->where(function ($query) use ($userID) {
                                $query->where('user_id', '==', $userID);
                            }),]

        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error_message = implode(' ', $errors);
            return response()->json([
                'ok' => false,
                'error' => $error_message
            ],404);  
        }
        
        $task_map = TaskMapping::editMapStatus($request['status'],$task_map_id);
        return response()->json([
            "ok" => true,
            "task_map" => $task_map,
            "message" => "Task is edited!"
        ], 200);
    }

    public function editStatusAdmin(Request $request,$task_id){
        $request['task_id'] = $task_id;
        $validator = Validator::make($request->all(),[
            'status' => 'required',
            'task_id' => 'required|exists:tasks,id'

        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error_message = implode(' ', $errors);
            return response()->json([
                'ok' => false,
                'error' => $error_message
            ],404);  
        }

        $task = Task::editStatus($request['status'],$task_id);
        $task_data = new TaskResponseResource($task);
        return response()->json([
            'ok' => true,
            'task' => $task_data
        ], 200);
    }

    public function deleteTask($task_id){
        $validator = Validator::make(['task_id' => $task_id], [
            'task_id' => 'exists:tasks,id',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error_message = implode(' ', $errors);
            return response()->json([
                'ok' => false,
                'error' => $error_message
            ],404);  
        }

        $message = Task::deleteTask($task_id);
        $resp_message = new TaskResponseResource($message);
        return response()->json([
            'ok' => true,
            'message' => $resp_message
        ], 200);
    }

    public function deleteMap($task_map_id){

        $validator = Validator::make(['task_map_id' => $task_map_id], [
            'task_map_id' => [
                'required',
                Rule::exists('task_mappings', 'id')->where(function ($query) {
                    $query->where('status', '!=', 1);
                }),
            ],
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error_message = implode(' ', $errors);
            return response()->json([
                'ok' => false,
                'error' => $error_message
            ],404);  
        }
        
        $message = TaskMapping::deleteTaskMap($task_map_id);
        $resp_message = new TaskResponseResource($message);
        return response()->json([
            'ok' => true,
            'message' => $resp_message
        ], 200);
    }

    
}
