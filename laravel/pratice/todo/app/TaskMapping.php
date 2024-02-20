<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Task;
use App\User;

class TaskMapping extends Model
{
    use SoftDeletes;
    public function users(){
        return $this->belongsTo(User::class,'id','user_id');
    }
    public function tasks(){
        return $this->belongsTo(Task::class,'id','task_id');
    }

    public static function get_user_task(Request $request,$user_id){
        $task = self::where('user_id', $user_id)->get();
        if(count($task)==0) return "No task is assigned";
        return $task;
    }

    public static function create_task_map(Request $request){
        //task_id, user_id, role
        $task_map = new TaskMapping();

        if(!isset($request['task_id'])){
            return "Task ID is Required!";
        }
        if(!isset($request['user_id'])){
            return "User ID is Required!";
        }
        if(!isset($request['role'])){
            return "Role is Required!";
        }

        $task = Task::where('id',($request['task_id']))->first();
        if(!isset($task)){
            $response = ["message" =>'No task with '.$request['task_id'].' is not found!'];
            return response($response, 200);
        }
        $user = User::where('id',($request['user_id']))->first();
        if(!isset($user)){
            $response = ["message" =>'No user with '.$request['user_id'].' is not found!'];
            return response($response, 200);
        }

        //Need to verify both task_id and user_id exists

        $task_map->task_id = $request['task_id'];
        $task_map->user_id = $request['user_id'];
        $task_map->role = $request['role'];
        $task_map->save();
        $response = ["message" =>'Task Mapping is created'];
        return response($response, 200);
    }

    public static function edit_map_task(Request $request,$task_map_id){
        if(!self::find($task_map_id)){
            $response = ["message" =>'No such task ID is found!'];
            return response($response, 200);
        }

        $edited_details = "";
        
        //task_id, user_id, role, status, completed_at
        if(isset($request['task_id'])){
            
            //checking whether the task id is present in database
            $task = Task::where('id',($request['task_id']))->first();
            if(!isset($task)){
                $response = ["message" =>'No task with '.$request['task_id'].' is not found!'];
                return response($response, 200);
            }

            self::where('id', $task_map_id)
                    ->update(['task_id' => $request['task_id']]);
            $edited_details .= "task_id ";
        }

        if(isset($request['user_id'])){
            
            //need to check whether the user_id available
            $user = User::where('id',($request['user_id']))->first();
            if(!isset($user)){
                $response = ["message" =>'No user with '.$request['user_id'].' is not found!'];
                return response($response, 200);
            }

            self::where('id', $task_map_id)
                    ->update(['user_id' => $request['user_id']]);
            $edited_details .= "user_id ";
        }
        
        if(isset($request['role'])){
            self::where('id', $task_map_id)
                    ->update(['role' => $request['role']]);
            $edited_details .= "role ";
        }
        
        if(isset($request['status']) && $request['status']==false){
            self::where('id', $task_map_id)
                    ->update([
                        'status' => $request['status'],
                        'time_completed' => date("Y-m-d H:i:s")
                    ]);
        }
        
        $response = ["message" =>'Task is edited. Edited task : '.$edited_details];
        return response($response, 200);
    }

    public static function edit_map_status(Request $request,$task_map_id){
        if(!self::find($task_map_id)){
            $response = ["message" =>'No such task map ID is found!'];
            return response($response, 200);
        }

        //check if the user ID and task ID matches
        $user = Auth::user();
        $user = $user->id;
        
        $check = self::where('user_id', $user)
                ->where('task_id',$task_map_id)->first();
        
        if(!isset($check)){
            return "No task match";
        }

        // update
        if($request['status']=='1'){
            self::where('user_id', $user)
            ->where('id',$task_map_id)
                ->update([
                    'status' => $request['status'],
                    'time_completed' => date("Y-m-d H:i:s")
                ]);
        }
        else{
            self::where('user_id', $user)
            ->where('id',$task_map_id)
                ->update([
                    'status' => $request['status'],
                    'time_completed' => null
                ]);
        }

        $response = ["message" =>'Status is updated'];
        return response($response, 200);
    }

    public static function delete_task_map(Request $request,$task_map_id)
    {
        if(!self::find($task_map_id)){
            $response = ["message" =>'No such task map ID is found!'];
            return response($response, 200);
        }

        self::where('id', $task_map_id)
        ->forceDelete();

        // Soft delete

        // self::withTrashed()
        // ->where('id', $task_map_id)
        // ->delete();

        $response = ["message" =>'Task is deleted'];
        return response($response, 200);

    }
}
