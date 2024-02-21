<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\TaskMapping;

class Task extends Model
{
    use SoftDeletes;
    public function task_mappings(){
        return $this->hasMany(TaskMapping::class,'id','task_id');
    }

    // Relationship: One-to-Many (Child Tasks)
    public function child_tasks()
    {
        return $this->hasMany(Task::class, 'parent_id', 'id');
    }
    
    // Relationship: Many-to-One (Parent Task)
    public function parent_task()
    {
        return $this->belongsTo(Task::class, 'parent_id', 'id');
    }

    public static function get_task(){
        $task = self::all();
        return $task;
    }
    public static function get_taskid($task_id){
        $task = self::where('id', $task_id)->first();
        return $task;
    }

    public static function create_task($task_data){
        $task = new Task();

        $task->title = $task_data['title'];
        $task->description = $task_data['description'];
        
        if(isset($task_data['urgency'])){
            $task->urgency = $task_data['urgency'];
        }
        
        if(isset($task_data['parent_id'])){
            $task->parent_id = $task_data['parent_id'];
        }
        
        $task->save();
        return $task;
    }

    public static function edit_task($task_data,$task_id){
        
        $edited_details = "";
        
        //title,description,urgency
        if(isset($task_data['title'])){
            self::where('id', $task_id)
                    ->update(['title' => $task_data['title']]);
            $edited_details .= "title, ";
        }

        if(isset($task_data['description'])){
            self::where('id', $task_id)
                    ->update(['description' => $task_data['description']]);
            $edited_details .= "description, ";
        }
        
        if(isset($task_data['urgency'])){
            self::where('id', $task_id)
                    ->update(['urgency' => $task_data['urgency']]);
            $edited_details .= "urgency, ";
        }

        if(isset($task_data['parent_id'])){
            self::where('id', $task_id)
                    ->update(['parent_id' => $task_data['parent_id']]);
            $edited_details .= "parent_id, ";
        }
        
        $task =  Task::find($task_id);
        return $task;
    }

    public static function edit_status($status,$task_id){

        if($status=="1"){
            self::where('id', $task_id)
                    ->update([
                        'status' => $status,
                        'time_completed' => date("Y-m-d H:i:s")
                    ]);
        }
        else{
            self::where('id', $task_id)
                    ->update([
                        'status' => $status,
                        'time_completed' => null
                    ]);
        }

        $task = Task::find($task_id);
        return $task;
    }
    
    public static function delete_task($task_id)
    {
        self::where('id', $task_id)
            ->delete();

        TaskMapping::where('task_id',$task_id)
                ->delete();

        return 'Task with it\'s sub-task are deleted';
    }
}
