<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\TaskMapping;

class Task extends Model
{
    use SoftDeletes;
    public function task_mappings(){
        return $this->hasMany(TaskMapping::class,'id','task_id');
    }

    // Relationship: One-to-Many (Child Tasks)
    public function childTasks()
    {
        return $this->hasMany(Task::class, 'parent_id', 'id');
    }
    
    // Relationship: Many-to-One (Parent Task)
    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_id', 'id');
    }

    public static function get_task(Request $request){
        $task = self::all();
        return $task;
    }
    public static function get_taskid(Request $request,$task_id){
        $task = self::where('id', $task_id)->first();
        return $task;
    }

    public static function create_task(Request $request){
        $task = new Task();
        if(!isset($request['title'])){
            return "Title is Required!";
        }
        if(!isset($request['description'])){
            return "Description is Required!";
        }

        $task->title = $request['title'];
        $task->description = $request['description'];
        
        if(isset($request['urgency'])){
            $task->urgency = $request['urgency'];
        }
        
        if(isset($request['parent_id'])){
            //check if the parent_id exits
            if(!self::where('id', $request['parent_id'])->exists()){
                return "No such parent task with ID ".$request['parent_id']." found!";
            }
            $task->parent_id = $request['parent_id'];
        }
        
        $task->save();
        $response = ["message" =>'Task is created'];
        return response($response, 200);
    }

    public static function edit_task(Request $request,$task_id){
        
        if(!self::find($task_id)){
            $response = ["message" =>'No such task ID is found!'];
            return response($response, 200);
        }

        $edited_details = "";
        
        //title,description,urgency
        if(isset($request['title'])){
            self::where('id', $task_id)
                    ->update(['title' => $request['title']]);
            $edited_details .= "title, ";
        }

        if(isset($request['description'])){
            self::where('id', $task_id)
                    ->update(['description' => $request['description']]);
            $edited_details .= "description, ";
        }
        
        if(isset($request['urgency'])){
            self::where('id', $task_id)
                    ->update(['urgency' => $request['urgency']]);
            $edited_details .= "urgency, ";
        }

        if(isset($request['parent_id'])){
            self::where('id', $task_id)
                    ->update(['parent_id' => $request['parent_id']]);
            $edited_details .= "parent_id, ";
        }
        
        $response = ["message" =>'Task is edited. Edited task : '.$edited_details];
        return response($response, 200);
    }

    public static function edit_status(Request $request,$task_id){

        //status
        if(!self::find($task_id)){
            $response = ["message" =>'No such task ID is found!'];
            return response($response, 200);
        }

        if(!isset($request['status'])){
            return "Status is Required!";
        }

        if($request['status']=="1"){
            self::where('id', $task_id)
                    ->update([
                        'status' => $request['status'],
                        'time_completed' => date("Y-m-d H:i:s")
                    ]);
        }
        else{
            self::where('id', $task_id)
                    ->update([
                        'status' => $request['status'],
                        'time_completed' => null
                    ]);
        }

        $response = ["message" =>'Status is updated'];
        return response($response, 200);
    }
    
    public static function delete_task(Request $request,$task_id)
    {
        if(!self::find($task_id)){
            $response = ["message" =>'No such task ID is found!'];
            return response($response, 200);
        }

        $subTask = self::where('parent_id',$task_id)->first();
        if(isset($subTask)){
            return "Can't delete, it has subtasks";
        }
        self::where('id', $task_id)
            ->delete();

        TaskMapping::where('task_id',$task_id)
                ->delete();
        
        // Soft delete

        // self::withTrashed()
        // ->where('id', $task_id)
        // ->delete();

        $response = ["message" =>'Task with it\'s sub-task are deleted'];
        return response($response, 200);

    }
}
