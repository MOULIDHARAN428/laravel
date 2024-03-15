<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Task;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskMapping extends Model
{
    use SoftDeletes;
    public function users(){
        return $this->belongsTo(User::class,'id','user_id');
    }
    public function tasks(){
        return $this->belongsTo(Task::class,'id','task_id');
    }

    public static function getUserTask($user_id){
        $tasks = self::where('user_id', $user_id)->get();
        for($i = 0; $i < count($tasks); $i++){
            $task_title = Task::where('id',$tasks[$i]->task_id)->first(['title']);
            $tasks[$i]['task_title'] = $task_title;
        }
        $tasks_rev = $tasks->reverse();
        $tasks_rev['user'] = User::where('id',$user_id)->get(['name','profile_picture']);
        return $tasks_rev;
    }

    public static function getAssignes($task_id){
        $assignes = self::where('task_id',$task_id)->get();
        $assignes_with_name = [];
        foreach($assignes as $assigne){
            $assigne_user_data = $assigne->toArray();
            $user_id = $assigne_user_data['user_id'];
            $user = User::find($user_id);
            $assigne_user_data['name'] = $user->name;
            $assigne_user_data['profile_picture'] = $user->profile_picture;
            $assignes_with_name[$user_id] = $assigne_user_data;
        }
        return $assignes_with_name;

    }

    public static function createTaskMap($map_data){
        //user_id, task_id, id

        $user_id = User::where('email',$map_data['user_email'])->value('id');

        $task_map = new TaskMapping();
        $task_map->task_id = $map_data['task_id'];
        $task_map->user_id = $user_id;
        $task_map->role = $map_data['role'];
        $task_map->assigned_at = $map_data['assigned_at'];

        if(isset($map_data['parent_id'])){
            $task_map->parent_id = $map_data['parent_id'];
        }

        $task_map->save();

        // incrementing the yet_to_do task
        UserTaskAnalytic::where('user_id', $map_data['user_id'])
                        ->increment('yet_to_do_task');

        $taskWithAssignes = Task::where('id', $map_data['task_id'])
                            ->with('taskMappings')
                            ->first();
                
        return $taskWithAssignes;
    }

    public static function editMapTask($map_data,$task_map_id){
        
        if(isset($map_data['user_email'])){
            $old_user = self::where('id', $task_map_id)->pluck('user_id');
            $new_user = User::where('email',$map_data['user_email'])->get(['id']);
            $new_user_id = $new_user[0]['id'];
            self::where('id', $task_map_id)
                    ->update(['user_id' => $new_user_id]);
            
            //decrement
            UserTaskAnalytic::where('user_id', $old_user)
                        ->decrement('yet_to_do_task');

            //increment
            UserTaskAnalytic::where('user_id', $new_user_id)
                        ->increment('yet_to_do_task');

            $task_map = self::find($task_map_id);
            $task_map['old_user'] = $old_user[0];
            $task_map['user_change'] = "edit";
            return $task_map;
        }

        $editableFields = ['task_id', 'role', 'assigned_at','time_completed'];
        foreach ($editableFields as $field) {
            if (isset($map_data[$field])) {
                self::where('id', $task_map_id)->update([$field => $map_data[$field]]);
            }
        }
        
        $task_map = self::find($task_map_id);
        return $task_map;
    }

    public static function editMapStatus($status,$task_map_id){
        $user = Auth::user();
        $userID = $user->id;
        $task_map = self::find($task_map_id);
        
        // update
        if($status=='1' && $task_map['status']=='0'){

                    
            UserTaskAnalytic::where('user_id', $userID)
                    ->decrement('yet_to_do_task');
            UserTaskAnalytic::where('user_id', $userID)
                    ->increment('weekly_complete_task');
            UserTaskAnalytic::where('user_id', $userID)
                    ->increment('monthly_complete_task');
            UserTaskAnalytic::where('user_id', $userID)
                    ->increment('quaterly_complete_task',1);
            UserTaskAnalytic::where('user_id', $userID)
                    ->increment('completed_task',1);
            
            self::where('id',$task_map_id)
                ->update([
                    'status' => $status,
                    'time_completed' => date("Y-m-d H:i:s")
                ]);
            
            
            // check for every assignes have completed task
            $flag = true;
            $task_id = $task_map->task_id;
            $tasks = Self::where('task_id','task_id')->get(['status']);
            foreach($tasks as $task){
                if($task->status == "0"){
                    $flag = false;
                    break;
                }
            }

            if($flag){
                Task::where('id',$task_id)->update([
                    'status' => $status,
                    'time_completed' => date("Y-m-d H:i:s")
                ]);

                //check for parent_task
                $parent_id = Task::where('id',$task_id)->value('parent_id');
                if($parent_id){
                    $sub_tasks = Task::where('parent_id',$parent_id)->get(['status']);
                    foreach($sub_tasks as $sub_task){
                        if($sub_task->status==0){
                            $flag = false;
                            break;
                        }
                    }
                    if($flag){
                        Task::where('id',$parent_id)->update([
                            'status' => $status,
                            'time_completed' => date("Y-m-d H:i:s")
                        ]);
                    }
                }
            }
            
        }
        
        else if($status=='0' && $task_map['status']=='1'){
            
            UserTaskAnalytic::where('user_id', $userID)
                ->increment('yet_to_do_task');
            UserTaskAnalytic::where('user_id', $userID)
                ->decrement('weekly_complete_task');
            UserTaskAnalytic::where('user_id', $userID)
                ->decrement('monthly_complete_task');
            UserTaskAnalytic::where('user_id', $userID)
                ->decrement('quaterly_complete_task');
            UserTaskAnalytic::where('user_id', $userID)
                ->decrement('completed_task');

            self::where('id',$task_map_id)
                ->update([
                    'status' => $status,
                    'time_completed' => null
                ]);
            
            // check for every assignes have completed task
            $flag = true;
            $task_id = $task_map->task_id;
            
            Task::where('id',$task_id)
                ->update([
                    'status' => $status,
                    'time_completed' => null
                ]);
            
            $parent_id = Task::where('id',$task_id)->value('parent_id');
            if($parent_id){
                Task::where('id',$parent_id)
                ->update([
                    'status' => $status,
                    'time_completed' => null
                ]);
            }
            
        }

        $taskWithAssignes = Task::where('id',$task_map['task_id'])
                            ->with('taskMappings')
                            ->first();
                
        return $taskWithAssignes;
    }

    public static function deleteTaskMap($task_map_id)
    {
        $user_id = self::where('id', $task_map_id)->pluck('user_id');
        $id_for_mail_content = self::where('id',$task_map_id)
                    ->get(['user_id','task_id','id'])->first();
        UserTaskAnalytic::where('user_id', $user_id)
                        ->decrement('yet_to_do_task');
        // self::where('id', $task_map_id)
        //     ->delete();
        
        return $id_for_mail_content;

    }
}
