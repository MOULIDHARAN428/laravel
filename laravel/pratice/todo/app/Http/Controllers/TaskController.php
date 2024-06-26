<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskMappingResponseResource;
use App\Http\Resources\TaskResponseResource;
use App\Http\Resources\TaskResponseResourceTemp;
use App\Http\Resources\TaskResponseWithAssignesResource;
use App\Imports\UploadDataFromExcel;
use App\Jobs\SendTaskDeleteEmail;
use App\Task;
use App\TaskMapping;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Traits\sendMailTrait;
use App\UserTaskAnalytic;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class TaskController extends Controller
{
    use sendMailTrait;
    //CRUD

    public function getTasks(){

        $tasks = Task::getTasks();

        return response()->json([
            'ok' => true,
            'task' => count($tasks) > 0 ? TaskResponseWithAssignesResource::collection($tasks) : "No task"
        ], 200);

    }
    public function getSpecificTask($task_id){
        $validator = Validator::make(['task_id' => $task_id], [
            'task_id' => 'integer|exists:tasks,id',
        ]);
        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors()); 
        }

        $task = Task::getTaskID($task_id);
        return response()->json([
            'ok' => true,
            'task' => TaskResponseWithAssignesResource::make($task)
        ], 200);
    }
    
    
    public function getUserTasks($user_id){
        $validator = Validator::make(['user_id' => $user_id], [
            'user_id' => 'integer|exists:users,id|exists:task_mappings,user_id'
        ]);

        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors()); 
        }

        $tasks = TaskMapping::getUserTask($user_id);
        return response()->json([
            'ok' => true,
            'task' => TaskMappingResponseResource::collection($tasks),
            'user' => $tasks['user'],
        ], 200);
    }

    public function getTasksWithSubTasks(){
        $task = Task::getTasksWithSubTasks();
        return response()->json([
            'ok' => true,
            'task' => $task['tasks'],
            'pagination' => $task['pagination']
        ], 200);
    }

    public function getTaskWithUsers($task_id){

        $validator = Validator::make(['task_id' => $task_id], [
            'task_id' => [
                'required',
                Rule::exists('tasks', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
        ]);

        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors()); 
        }

        $task = Task::getTaskWithUsers($task_id);
        return response()->json([
            'ok' => true,
            'task' => $task
        ], 200);    
    }

    public function getTaskWithSubtask($task_id){
        $validator = Validator::make(['task_id' => $task_id], [
            'task_id' => [
                'required',
                Rule::exists('tasks', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
        ]);
        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors()); 
        }
        $task = Task::getTaskWithSubtask($task_id);
        return response()->json([
            'ok' => true,
            'task' => $task
        ], 200);
    }

    public function getUsersWithProfile(){
        $users = User::getUsersWithProfile();
        return response()->json([
            'ok' => true,
            'users' => $users
        ]);
    }

    public function getUserAnalytics(){
        $user_id = auth()->user()->id;
        $validator = Validator::make(["user_id"=>$user_id],[
            'user_id'=>'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors());
        }

        $user_analytics = UserTaskAnalytic::getUserAnalytics($user_id);
        return response()->json([
            'ok' => true,
            'analytics' => $user_analytics
        ], 200);
    }

    public function sortByTime(Request $request){
        $validator = Validator::make($request->all(), [
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
        ], [
            'end_time.after' => 'The end time must be after the start time.',
        ]);
        if ($validator->fails()) {
            $errorMessages = [];
            foreach ($validator->errors()->messages() as $fieldName => $messages) {
                $errorMessages[$fieldName] = [$messages[0]];
            }
        
            return $this->appendAndSendErrorMessage($errorMessages);
        }
        
        
        $task = Task::sortByTime($request);
        return response()->json([
            'ok' => true,
            'task' => $task
        ], 200);
    }
    public function createTask(Request $request){

        $validator = Validator::make($request->all(),[
            'title' => 'required|max:100',
            'description' => 'required|max:100',
            'due_time'=> 'required|date_format:Y-m-d H:i:s',
            'urgency' => 'required',
            'parent_id'=> 'sometimes|integer|exists:tasks,id'
        ]);
        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors());
        }
        $task = Task::createTask($request);

        return response()->json([
            'ok' => true,
            'task' => TaskResponseWithAssignesResource::make($task)
        ], 200);
    }

    public function assignTask(Request $request){
        $validator = Validator::make($request->all(),[
            'task_id' => 'required|exists:tasks,id',
            'user_email' => 'required|exists:users,email',
            'role'=> 'required|max:100',
            'assigned_at' => 'required|date_format:Y-m-d H:i:s'

        ]);
        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors()); 
        }

        $task_map = TaskMapping::createTaskMap($request);
        
        //Mail
        //for assigned user
        $last_assignes = $task_map['taskMappings']->last();

        $this->sendAssignMail($last_assignes);
        
        //notification for remaining user that are assigned in the task
        $assignes = $task_map['taskMappings'];
        foreach($assignes as $assigne){
            if($assigne['user_id']!=$last_assignes['user_id']){
                $this->sendAssignNotificationMail($assigne['user_id']);
            }
        }

        return response()->json([
            'ok' => true,
            'task' => TaskResponseWithAssignesResource::make($task_map)
        ], 200);
    }

    public function importExcel(Request $request){
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls|max:2048', // Adjust file validation rules as needed
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors());
        }
        
        $file = $request->file('file');
        Excel::import(new UploadDataFromExcel,$file);
        return response()->json([
            "ok" => true,
            "message"=>"uploaded successfully"
        ],200);
    }

    public function editTask(Request $request,$task_id){
        $validator = Validator::make(array_merge(['task_id' => $task_id], $request->all()), [
            'task_id' => 'required|integer|exists:tasks,id',
            'title' => 'required|max:100',
            'description' => 'required|max:100',
            'due_time'=> 'sometimes|date_format:Y-m-d H:i:s',
            'urgency' => 'required',
            'parent_id'=> 'sometimes|integer|exists:tasks,id'
        ]);
        
        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors());  
        }
        
        $task_map = Task::editTask($request,$task_id);
        return response()->json([
            'ok' => true,
            'task' => TaskResponseWithAssignesResource::make($task_map)
        ], 200);
    }

    public function editMapTask(Request $request,$task_map_id){
        $request['task_map_id'] = $task_map_id;
        $validator = Validator::make($request->all(),[
            'user_email' => 'nullable|exists:users,email',
            'role' => 'sometimes|max:100',
            'assigned_at' => 'nullable|date_format:Y-m-d H:i:s',
            'task_map_id' => ['required',
                                Rule::exists('task_mappings', 'id')->where(function ($query) {
                                    $query->whereNull('deleted_at');
                                })],
            

        ]);
        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors());  
        }

        $task_map = TaskMapping::editMapTask($request,$task_map_id);
        if(!isset($task_map['old_user'])){
            $this->sendEditMail($task_map);
        }
        else{
            //assign mail
            $this->sendAssignMail($task_map);
            
            //delete mail
            $task_map['user_id'] = $task_map['old_user'];
            $this->sendDeleteMail($task_map);
        }
        
        return response()->json([
            'ok' => true,
            'task' => TaskMappingResponseResource::make($task_map)
        ], 200);
    }

    public function editMapStatus(Request $request,$task_map_id){
        $request['task_map_id'] = $task_map_id;
        $user = Auth::user();
        $userID = $user->id;
        
        $validator = Validator::make($request->all(),[
            'status' => 'required|boolean',
            'task_map_id' => ['required',
                            Rule::exists('task_mappings', 'id')->where(function ($query) use ($userID) {
                                $query->where('user_id', '=', $userID);
                            }),]

        ]);
        
        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors());  
        }
        
        $taskWithAssignes = TaskMapping::editMapStatus($request['status'],$task_map_id);

        return response()->json([
            "ok" => true,
            "task_map" => TaskResponseWithAssignesResource::make($taskWithAssignes),
            "message" => "Task is edited!"
        ], 200);
    }

    public function editStatusAdmin(Request $request,$task_id){
        $request['task_id'] = $task_id;
        $validator = Validator::make($request->all(),[
            'status' => 'required|boolean',
            'task_id' => 'required|exists:tasks,id'

        ]);

        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors());  
        }

        $taskWithAssignes = Task::editStatus($request['status'],$task_id);
        return response()->json([
            'ok' => true,
            'task' => TaskResponseWithAssignesResource::make($taskWithAssignes)
        ], 200);
    }

    public function deleteTask($task_id){
        
        // $validator = Validator::make(['task_id' => $task_id], [
        //     'task_id' => [
        //         'required',
        //         Rule::exists('tasks', 'id')->where(function ($query) {
        //             $query->where('status', '=', 0)
        //             ->whereNull('deleted_at');
        //         }),
        //     ],
        // ]);
        
        $validator = Validator::make(['task_id' => $task_id], [
            'task_id' => 'required|exists:tasks,id',
        ]);
        
        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors());  
        }
        Task::deleteTask($task_id);

        return response()->json([
            'ok' => true,
            'message' => "Tasks with its sub task has been deleted"
        ], 200);
    }

    public function deleteMap($task_map_id){

        $validator = Validator::make(['task_map_id' => $task_map_id], [
            'task_map_id' => [
                'required',
                Rule::exists('task_mappings', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
        ]);
        
        if ($validator->fails()) {
            return $this->appendAndSendErrorMessage($validator->errors()); 
        }

        $id_for_mail_content = TaskMapping::deleteTaskMap($task_map_id);
        $this->sendDeleteMail($id_for_mail_content);
        
        return response()->json([
            'ok' => true,
            'message' => "The task has been deleted!"
        ], 200);
    }

    public function appendAndSendErrorMessage($errors){
        return response()->json([
            'ok' => false,
            'validation_errors' => $errors
        ], 422);
    } 
}
