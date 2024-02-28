<?php

namespace App;

use App\Jobs\SendTaskAssignEmail;
use App\Jobs\SendTaskDeleteEmail;
use App\Jobs\SendTaskEditEmail;
use App\Mail\TaskAssigned;
use App\Mail\TaskDeleted;
use App\Mail\TaskEdited;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Task;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

    public static function createTaskMap($map_data){
        $task_map = new TaskMapping();
        $task_map->task_id = $map_data['task_id'];
        $task_map->user_id = $map_data['user_id'];
        $task_map->role = $map_data['role'];
        $task_map->assigned_at = $map_data['assigned_at'];
        $task_map->save();

        // incrementing the yet_to_do task
        UserTaskAnalytic::where('user_id', $map_data['user_id'])
                        ->increment('yet_to_do_task');

        // mail
        $map_data['id'] = $task_map->id;
        $user_data = User::where('id',$map_data['user_id'])->get(['name','email'])->first();
        $task_data = Task::where('id',$map_data['task_id'])->get(['title','due_time'])->first();
        SendTaskAssignEmail::dispatch($user_data->toArray(),$task_data->toArray(),$map_data->toArray())->onQueue('emails');
        
        return $task_map;
    }

    public static function editMapTask($map_data,$task_map_id){
        
        if(isset($map_data['user_id'])){
            $map_detail_cur = self::where('id', $task_map_id)->first(['user_id','task_id']);

            $task_deatils = Task::where('id',$map_detail_cur->task_id)->first(['title','due_time']);
            $map_deatils  = Self::where('id',$task_map_id)->first(['id','role','assigned_at']);
            
            //deletion of task mail
            $user_name_delete = User::where('id',$map_detail_cur->user_id)->first(['name','email']);
            SendTaskDeleteEmail::dispatch($user_name_delete->toArray(),$task_deatils->toArray(),$map_deatils->toArray())->onQueue('emails');

            //creation of task mail
            $user_name_create = User::where('id',$map_data['user_id'])->first(['name','email']);
            SendTaskAssignEmail::dispatch($user_name_create->toArray(),$task_deatils->toArray(),$map_deatils->toArray())->onQueue('emails');

            self::where('id', $task_map_id)
                    ->update(['user_id' => $map_data['user_id']]);
            
            //decrement
            UserTaskAnalytic::where('user_id', $map_detail_cur->user_id)
                        ->decrement('yet_to_do_task');

            //increment
            UserTaskAnalytic::where('user_id', $map_data['user_id'])
                        ->increment('yet_to_do_task');

            $task_map = self::find($task_map_id);
            return $task_map;
        }
        
        if(isset($map_data['status']) && $map_data['status']==false){
            self::where('id', $task_map_id)
                    ->update([
                        'status' => $map_data['status'],
                        'time_completed' => date("Y-m-d H:i:s")
                    ]);
        }

        $editableFields = ['task_id', 'role', 'assigned_at'];
        foreach ($editableFields as $field) {
            if (isset($map_data[$field])) {
                self::where('id', $task_map_id)->update([$field => $map_data[$field]]);
            }
        }
        
        $ID = Self::where('id',$task_map_id)->first(['user_id','task_id']);
        $user_details = User::where('id',$ID['user_id'])->get(['name','email'])->first();
        $task_details = Task::where('id',$ID['task_id'])->get(['title','due_time'])->first();
        $task_map = self::find($task_map_id);

        SendTaskEditEmail::dispatch($user_details->toArray(), $task_details->toArray(), $task_map->toArray())->onQueue('emails');

        return $task_map;
    }

    public static function editMapStatus($status,$task_map_id){
        $user = Auth::user();
        $userID = $user->id;
        $status_cur = self::find($task_map_id);
        // update
        
        if($status=='1' && $status_cur['status']=='0'){

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
        
        else if($status=='0' && $status_cur['status']=='1'){

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

        $task_map = self::find($task_map_id);
        return $task_map;
    }

    public static function deleteTaskMap($task_map_id)
    {
        $ID = self::where('id',$task_map_id)
                    ->get(['user_id','task_id','role'])->first();
        UserTaskAnalytic::where('user_id', $ID['user_id'])
                        ->decrement('yet_to_do_task');
        
        //mail
        
        $user_details = User::where('id',$ID['user_id'])->get(['name','email'])->first();
        $task_details = Task::where('id',$ID['task_id'])->get(['title','due_time'])->first();
        $map_details  = Self::where('id',$task_map_id) ->get(['id','role'])->first();

        SendTaskDeleteEmail::dispatch($user_details->toArray(),$task_details->toArray(),$map_details->toArray())->onQueue('emails');

        self::where('id', $task_map_id)
            ->delete();
        
        return "Task Map is deleted";

    }
}
