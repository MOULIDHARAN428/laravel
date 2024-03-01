<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\TaskMapping;

class Task extends Model
{
    use SoftDeletes;
    public function taskMappings(){
        return $this->hasMany(TaskMapping::class,'task_id','id');
    }

    // Relationship: One-to-Many (Child Tasks)
    public function childTasks()
    {
        return $this->hasMany(Task::class, 'id', 'parent_id');
    }
    
    // Relationship: Many-to-One (Parent Task)
    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_id', 'id');
    }

    public static function getTasks(){
        $tasks = self::query()->with('taskMappings')->get();
        return $tasks;
    }
    
    public static function getTaskID($task_id){
        $task = self::where('id', $task_id)
                ->with('taskMappings')
                ->first();
        return $task;
    }

    public static function createTask($task_data){
        $task = new Task();

        $task->title = $task_data['title'];
        $task->description = $task_data['description'];
        $task->due_time = $task_data['due_time'];
        
        if(isset($task_data['urgency'])){
            $task->urgency = $task_data['urgency'];
        }
        
        if(isset($task_data['parent_id'])){
            $task->parent_id = $task_data['parent_id'];
        }
        $task->save();

        $taskWithAssignes = self::where('id', $task->id)
                ->with('taskMappings')
                ->first();
        
        return $taskWithAssignes;
    }

    public static function editTask($task_data,$task_id){
        $editableFields = ['title', 'description', 'urgency', 'parent_id', 'due_time'];
        
        foreach ($editableFields as $field) {
            if (isset($task_data[$field])) {
                self::where('id', $task_id)->update([$field => $task_data[$field]]);
            }
        }
        
        $taskWithAssignes = self::where('id', $task_id)
                ->with('taskMappings')
                ->first();
        
        return $taskWithAssignes;
    }

    public static function editStatus($status,$task_id){
        
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
        $taskWithAssignes = self::where('id', $task_id)
                ->with('taskMappings')
                ->first();
        
        return $taskWithAssignes;
    }
    
    public static function deleteTask($task_id)
    {
        $sub_task_ID = self::where('parent_id', $task_id)
                    ->pluck('id')
                    ->toArray();

        array_push($sub_task_ID, $task_id);
        
        foreach($sub_task_ID as $taskID){

            //recursive function
            if($taskID != $task_id){
                self::deleteTask($sub_task_ID);
            }
            
            self::where('id', $taskID)
                  ->delete();

            $usersID = TaskMapping::where('task_id', $taskID)
                        ->pluck('user_id')
                        ->toArray();

            TaskMapping::where('task_id',$taskID)
                         ->delete();
            
            foreach($usersID as $userID){
                UserTaskAnalytic::where('user_id', $userID)
                        ->decrement('yet_to_do_task');
            }
        }

        return 'Task with it\'s sub-task are deleted';
    }
}
