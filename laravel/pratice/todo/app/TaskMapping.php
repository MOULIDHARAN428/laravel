<?php

namespace App;

use App\Mail\TaskAssigned;
use App\Mail\TaskDeleted;
use App\Mail\TaskEdited;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Task;
use App\User;
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

    public function send_task_assign_mail($user,$task,$map){
        //name, map_id, task_title, role, due_time, assigned time

        Mail::to($user['email'])->send(new TaskAssigned($task,$user,$map));
    }
    public function send_task_delete_mail($user,$task,$map){
        //name, map_id, task_title, role

        Mail::to($user['email'])->send(new TaskDeleted($task,$user,$map));
    }
    public function send_task_edit_mail($user,$task,$map){
        //name, email, title, due_time, $map['id'],role, assigned_at

        Mail::to($user['email'])->send(new TaskEdited($task,$user,$map));
    }

    public static function create_task_map($map_data){
        $task_map = new TaskMapping();
        $task_map->task_id = $map_data['task_id'];
        $task_map->user_id = $map_data['user_id'];
        $task_map->role = $map_data['role'];
        $task_map->assigned_at = $map_data['assigned_at'];
        $task_map->save();
        
        //incrementing the yet_to_do task
        UserTaskAnalytic::where('user_id', $map_data['user_id'])
                        ->increment('yet_to_do_task');
        
        //mail
        //data : user-name, task_title, role, assigned_at, due_time
        $user_name = User::where('id',$map_data['user_id'])->pluck(['name','email']);
        $task = Task::where('id',$map_data['task_id'])->pluck(['title','due_time']);

        self::send_task_assign_mail($user_name,$task,$map_data);
        
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
            $userID = self::where('id', $task_map_id)->pluck('user_id');
            self::where('id', $task_map_id)
                    ->update(['user_id' => $map_data['user_id']]);

            //delete
            // $user_name,$task_map_id,$task_details
            // name, map_id, task_title, role
            $task = Task::where('id',$map_data['task_id'])->pluck(['title','due_time']);
            $map  = Self::where('id',$task_map_id)->pluck(['id','role','assigned_at']); 
            $user_name_delete = User::where('id',$map_data['user_id'])->pluck(['name','email']);

            Self::send_task_delete_mail($user_name_delete,$task,$map);
            //create
            $user_name_create = User::where('id',$map_data['user_id'])->pluck(['name','email']);

            Self::send_task_delete_mail($user_name_create,$task,$map);

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

        $ID = Self::where('id',$task_map_id)->get(['user_id','task_id']);
        $user = User::where('id',$ID['userID'])->pluck(['name','email']);
        $task = Task::where('id',$ID['task_id'])->pluck('title','due_time');
        $map = Self::find($mapID);
        Self::send_task_edit_mail($user,$task,$map);

        $task_map = self::find($task_map_id);
        return $task_map;
    }

    public static function edit_map_status($status,$task_map_id){
        
        // update
        if($status=='1'){
            $userID = self::where('id',$task_map_id)
                    ->pluck('user_id');
            
            UserTaskAnalytic::where('user_id', $userID)
                    ->decrement('yet_to_do_task')
                    ->increment('weekly_complete_task')
                    ->increment('monthly_complete_task')
                    ->increment('quaterly_complete_task')
                    ->increment('completed_task');

            self::where('id',$task_map_id)
                ->update([
                    'status' => $status,
                    'time_completed' => date("Y-m-d H:i:s")
                ]);
            
        }
        else{
            $userID = self::where('id',$task_map_id)
                    ->pluck('user_id');
            UserTaskAnalytic::where('user_id', $userID)
                ->increment('yet_to_do_task')
                ->decrement('weekly_complete_task')
                ->decrement('monthly_complete_task')
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
                    ->pluck(['user_id','task_id','role']);
        UserTaskAnalytic::where('user_id', $ID['user_id'])
                        ->decrement('yet_to_do_task');
        
        //mail
        //name, map_id, task_title, role
        $user = User::where('id',$ID['user_id'])->pluck(['name','email']);
        $task = Task::where('id',$ID['task_id'])->pluck(['title','due_time']);
        $map  = Self::where('id',$task_map_id) ->pluck(['id','role']);
        // $map_data,$user_name,$task
        Self::send_task_delete_mail($user,$task,$map);


        self::where('id', $task_map_id)
            ->delete();
        
        return "Task Map is deleted";

    }
}
