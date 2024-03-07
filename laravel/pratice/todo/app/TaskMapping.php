<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Task;
use App\User;
use Illuminate\Support\Facades\Auth;

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
        return $tasks;
    }

    public static function getAssignes($task_id){
        $assignes = self::where('task_id',$task_id)->get();
        $assignes_with_name = [];
        foreach($assignes as $assigne){
            $assigne_user_data = $assigne->toArray();
            $user_id = $assigne_user_data['user_id'];
            $user = User::find($user_id);
            $assigne_user_data['name'] = $user->name;
            $assignes_with_name[$user_id] = $assigne_user_data;
        }
        return $assignes_with_name;

    }

    public static function createTaskMap($map_data){
        //user_id, task_id, id
        $task_map = new TaskMapping();
        $task_map->task_id = $map_data['task_id'];
        $task_map->user_id = $map_data['user_id'];
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
        
        if(isset($map_data['user_id'])){
            $old_user = self::where('id', $task_map_id)->pluck('user_id');
            self::where('id', $task_map_id)
                    ->update(['user_id' => $map_data['user_id']]);
            
            //decrement
            UserTaskAnalytic::where('user_id', $old_user)
                        ->decrement('yet_to_do_task');

            //increment
            UserTaskAnalytic::where('user_id', $map_data['user_id'])
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

            // UserTaskAnalytic::where('user_id', $userID)->update([
            //     'yet_to_do_task' => DB::raw('yet_to_do_task - 1'),
            //     'weekly_complete_task' => DB::raw('weekly_complete_task + 1'),
            //     'monthly_complete_task' => DB::raw('monthly_complete_task + 1'),
            //     'quaterly_complete_task' => DB::raw('quaterly_complete_task + 1'),
            //     'completed_task' => DB::raw('completed_task + 1'),
            // ]);
                    
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
            
        }
        
        else if($status=='0' && $task_map['status']=='1'){

            // UserTaskAnalytic::where('user_id', $userID)->update([
            //     'yet_to_do_task' => DB::raw('yet_to_do_task + 1'),
            //     'weekly_complete_task' => DB::raw('weekly_complete_task - 1'),
            //     'monthly_complete_task' => DB::raw('monthly_complete_task - 1'),
            //     'quaterly_complete_task' => DB::raw('quaterly_complete_task - 1'),
            // ]);
            
            UserTaskAnalytic::where('user_id', $userID)
                ->increment('yet_to_do_task');
            UserTaskAnalytic::where('user_id', $userID)
                ->decrement('weekly_complete_task');
            UserTaskAnalytic::where('user_id', $userID)
                ->decrement('monthly_complete_task');
            UserTaskAnalytic::where('user_id', $userID)
                ->decrement('quaterly_complete_task');
            
            self::where('id',$task_map_id)
                ->update([
                    'status' => $status,
                    'time_completed' => null
                ]);
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
