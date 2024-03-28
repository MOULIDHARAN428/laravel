<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\TaskMapping;
use App\Traits\formatTime;
use Illuminate\Support\Facades\Log;

class Task extends Model
{
    use SoftDeletes, formatTime;
    public function taskMappings(){
        return $this->hasMany(TaskMapping::class,'task_id','id');
    }

    // Relationship: One-to-Many (Child Tasks)
    public function subTasks()
    {
        return $this->hasMany(Task::class, 'parent_id', 'id');
    }
    
    // Relationship: Many-to-One (Parent Task)
    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'id', 'parent_id');
    }
    public static function getTasksWithSubTasks() {
        // Fetch tasks with eager loaded subtasks and paginate
        $tasks = self::with('subTasks')->paginate(6);
        // $tasks = $tasks->reverse();  
        $formattedTasks = [];
        
        foreach ($tasks as $task) {
            // Format time
            $formattedTask = $task->toArray();
            $formattedTask['time_completed'] = $task->formatTime($task['time_completed']);
            $formattedTask['due_time'] = $task->formatTime($task['due_time']);
    
            // Assignees for the main task
            $formattedTask['assignes'] = TaskMapping::getAssignes($task['id']);
    
            if ($task->parent_id === null) {
                // If the task is a parent task, add it to the formatted tasks
                $formattedTask['sub_tasks'] = [];
                $formattedTasks[] = $formattedTask;
            } else {
                // If the task is a subtask, add it to its parent task's sub_tasks array
                $parentTaskIndex = array_search($task->parent_id, array_column($formattedTasks, 'id'));
                if ($parentTaskIndex !== false) {
                    $subTask = $formattedTask;
                    // Assignees for the subtask
                    $subTask['assignes'] = TaskMapping::getAssignes($task['id']);
                    $formattedTasks[$parentTaskIndex]['sub_tasks'][] = $subTask;
                }
            }
        }

        $pagination = $tasks->links()->toHtml();
        // Return both the formatted tasks and the pagination object
        return [
            'tasks' => $formattedTasks,
            'pagination' => $pagination
        ];
    }
    
    
    public static function sortByTime($data){
        $tasks = Self::whereBetween('created_at', [$data['start_time'], $data['end_time']])
                ->get();

        $task_with_subtask = [];

        foreach ($tasks as $t) {
            $task =  $t->toArray();
            $formate_time = new Task();
            
            //formate time
            if(isset($task['time_completed'])){
                $task['time_completed'] = $formate_time->formatTime($task['time_completed']);
            }if(isset($task['due_time'])){
                $task['due_time'] = $formate_time->formatTime($task['due_time']);
            }

            //putting subtasks into task
            if (!isset($task_with_subtask[$task['parent_id']])) {
                $task['assignes'] = TaskMapping::getAssignes($task['id']);
                $task_with_subtask[$task['id']] = $task;
                $task_with_subtask[$task['id']]['sub_tasks'] = [];
            } else {
                $task['assignes'] = TaskMapping::getAssignes($task['id']);
                $task_with_subtask[$task['parent_id']]['sub_tasks'][] = $task;
            }
        }

        // to have the latest task first
        $task_with_subtask = array_reverse($task_with_subtask);

        return $task_with_subtask;
    }

    public static function getTaskWithUsers($task_id){
        $task = self::where('id',$task_id)->first();
        $formate_time = new Task();
        if(isset($task['time_completed'])){
            $task['time_completed'] = $formate_time->formatTime($task['time_completed']);
        }if(isset($task['due_time'])){
            $task['due_time'] = $formate_time->formatTime($task['due_time']);
        }
        
        $task['assignes'] = TaskMapping::getAssignes($task_id);
        return $task;
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

    public static function getTaskWithSubtask($task_id){
        $task['title'] = Self::where('id',$task_id)->value('title');
        $task_id = Self::where('parent_id',$task_id)->get(['id']);
        $task['sub_task'] = [];
        foreach($task_id as $id){
            $task['sub_task'][] = Self::getTaskWithUsers($id->id);
        }
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

    public static function analyticsDayData($data){
        $tasks = [];
        if(isset($data['from'])){
            $tasks = Self::where([
                ['time_completed', '>=', $data['from']],
                ['time_completed', '<=', $data['to']]
            ])->get(['time_completed']);
        }
        else{
            $tasks = Self::get(['time_completed']);
        }
        $array = [0,0,0,0,0,0,0];
        foreach($tasks as $task){
            if(!isset($task['time_completed'])){
                continue;
            }
            $dateTime = new \DateTime($task['time_completed']);;
            $dayOfWeekNumeric = $dateTime->format('N');
            $array[$dayOfWeekNumeric - 1]++;
        }
        return $array;
    }

    public static function analyticsHourData($data){
        $tasks = [];
        if(isset($data['from'])){
            $tasks = Self::where([
                ['time_completed', '>=', $data['from']],
                ['time_completed', '<=', $data['to']]
            ])->get(['time_completed']);
        }
        else{
            $tasks = Self::get(['time_completed']);
        }
        $array = [0,0,0,0,0,0,0,0,0,0,
                  0,0,0,0,0,0,0,0,0,0,
                  0,0,0,0];
        
        foreach($tasks as $task){
            if(!isset($task['time_completed'])){
                continue;
            }
            $array[date("g",strtotime($task['time_completed']))]++;
        }
        return $array;
    }

    public static function analyticsTaskAssignes($data){
        $tasks = [];
        if(isset($data['from'])){
            $tasks = Self::where([
                ['time_completed', '>=', $data['from']],
                ['time_completed', '<=', $data['to']]
            ])->get(['id','title']);
        }
        else{
            $tasks = Self::get(['id','title']);
        }
        foreach($tasks as $task){
            if(!isset($task['id'])){
                continue;
            }
            $assignes = TaskMapping::where('task_id',$task['id'])->count();
            $task['assignes']=$assignes;
        }

        return $tasks;
    }

    public static function analyticUserTasks(){
        $users = User::get(['id','name']);
        foreach($users as $user){
            $analytics = UserTaskAnalytic::where('user_id',$user['id'])->get(['yet_to_do_task','due_task','completed_task']);
            $user['assigned'] = $analytics[0]['yet_to_do_task'];
            $user['due'] = $analytics[0]['due_task'];
            $user['completed'] = $analytics[0]['completed_task'];
        }
        return $users;
    }

}
