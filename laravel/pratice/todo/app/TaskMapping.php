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

    public static function get_user_task($user_id){
        $task = self::where('user_id', $user_id)->get();
        return $task;
    }

    public static function send_task_assign_mail($user,$task,$map){
        SendTaskAssignEmail::dispatch($user, $task, $map)->onQueue('emails');
        // dispatch((new SendTaskAssignEmail($user,$task,$map))->onQueue('emails'));
    }
    public static function send_task_delete_mail($user,$task,$map){
        SendTaskDeleteEmail::dispatch($user, $task, $map)->onQueue('emails');
    }
    public static function send_task_edit_mail($user,$task,$map){
        SendTaskEditEmail::dispatch($user, $task, $map)->onQueue('emails');
    }

    public static function create_task_map($map_data){
        $task_map = new TaskMapping();
        $task_map->task_id = $map_data['task_id'];
        $task_map->user_id = $map_data['user_id'];
        $task_map->role = $map_data['role'];
        $task_map->assigned_at = $map_data['assigned_at'];
        // $task_map->save();

        //incrementing the yet_to_do task
        UserTaskAnalytic::where('user_id', $map_data['user_id'])
                        ->increment('yet_to_do_task');
        $map_data['id'] = $task_map->id;
        //mail
        $user = User::where('id',$map_data['user_id'])->get(['name','email'])->first();
        $task = Task::where('id',$map_data['task_id'])->get(['title','due_time'])->first();
        self::send_task_assign_mail($user->toArray(),$task->toArray(),$map_data->toArray());
        
        return $task_map;
    }

    public static function edit_map_task($map_data,$task_map_id){
        $edited_details = "";
        $mapID = $task_map_id;
        //task_id, user_id, role, status, completed_at
        if(isset($map_data['task_id'])){
            self::where('id', $task_map_id)
                    ->update(['task_id' => $map_data['task_id']]);
            $mapID = $map_data['task_id'];
            $edited_details .= "task_id ";
        }
        
        if(isset($map_data['user_id'])){
            $userID = self::where('id', $task_map_id)->pluck('user_id')->first();

            //delete
            // $user_name,$task_map_id,$task_details
            // name, map_id, task_title, role
            $task_id = Self::where('id',$task_map_id)->pluck('task_id');
            $task = Task::where('id',$task_id)->first(['title','due_time']);
            $map  = Self::where('id',$task_map_id)->first(['id','role','assigned_at']);
             
            $user_name_delete = User::where('id',$userID)->first(['name','email']);
            Self::send_task_delete_mail($user_name_delete->toArray(),$task->toArray(),$map->toArray());
            
            //create
            $user_name_create = User::where('id',$map_data['user_id'])->first(['name','email']);
            Self::send_task_assign_mail($user_name_create->toArray(),$task->toArray(),$map->toArray());

            self::where('id', $task_map_id)
                    ->update(['user_id' => $map_data['user_id']]);
            
            //decrement
            UserTaskAnalytic::where('user_id', $userID)
                        ->decrement('yet_to_do_task');

            //increment
            UserTaskAnalytic::where('user_id', $map_data['user_id'])
                        ->increment('yet_to_do_task');
            $edited_details .= "user_id ";

            $task_map = self::find($task_map_id);
            return $task_map;
        }
        
        if(isset($map_data['role'])){
            self::where('id', $task_map_id)
                    ->update(['role' => $map_data['role']]);
            $edited_details .= "role ";
        }

        if(isset($map_data['assigned_at'])){
            self::where('id', $task_map_id)
                    ->update(['assigned_at' => $map_data['assigned_at']]);
            $edited_details .= "assigned_at ";
        }
        
        if(isset($map_data['status']) && $map_data['status']==false){
            self::where('id', $task_map_id)
                    ->update([
                        'status' => $map_data['status'],
                        'time_completed' => date("Y-m-d H:i:s")
                    ]);
        }
        
        $ID = Self::where('id',$task_map_id)->first(['user_id','task_id']);
        $user = User::where('id',$ID['user_id'])->get(['name','email'])->first();
        $task = Task::where('id',$ID['task_id'])->get(['title','due_time'])->first();
        $task_map = self::find($task_map_id);
        Self::send_task_edit_mail($user->toArray(),$task->toArray(),$task_map->toArray());

        
        return $task_map;
    }

    public static function edit_map_status($status,$task_map_id){
        $user = Auth::user();
        $userID = $user->id;
        $status_cur = self::find($task_map_id);
        // update
        if($status=='1' && $status_cur['status']=='0'){
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

    public static function delete_task_map($task_map_id)
    {
        $ID = self::where('id',$task_map_id)
                    ->get(['user_id','task_id','role'])->first();
        UserTaskAnalytic::where('user_id', $ID['user_id'])
                        ->decrement('yet_to_do_task');
        
        //mail
        //name, map_id, task_title, role
        $user = User::where('id',$ID['user_id'])->get(['name','email'])->first();
        $task = Task::where('id',$ID['task_id'])->get(['title','due_time'])->first();
        $map  = Self::where('id',$task_map_id) ->get(['id','role'])->first();
        // $map_data,$user_name,$task
        Self::send_task_delete_mail($user->toArray(),$task->toArray(),$map->toArray());


        // self::where('id', $task_map_id)
        //     ->delete();
        
        return "Task Map is deleted";

    }
}
