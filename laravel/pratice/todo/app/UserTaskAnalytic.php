<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserTaskAnalytic extends Model
{
    use SoftDeletes;
    public function users(){
        return $this->belongsTo(User::class,'id','user_id');
    }
    public function yet_to_do_task($user_id){
        $yet_to_do = self::where('user_id',$user_id)
                          ->pluck('yet_to_do_task');
        return $yet_to_do;
    }
    public function due_task($user_id){
        $yet_to_do = self::where('user_id',$user_id)
                          ->pluck('due_task');
        return $yet_to_do;
    }
    public function completed_task($user_id){
        $yet_to_do = self::where('user_id',$user_id)
                          ->pluck('completed_task');
        return $yet_to_do;
    }
    public function weekly_completed_task_ratio($user_id){
        $yet_to_do = $this->yet_to_do($user_id);
        $completed_task = self::where('user_id',$user_id)
                            ->pluck('weekly_complete_task');
        return $completed_task/($completed_task+$yet_to_do);
    }
    public function monthly_completed_task_ratio($user_id){
        $yet_to_do = $this->yet_to_do($user_id);
        $completed_task = self::where('user_id',$user_id)
                            ->pluck('monthly_complete_task');
        return $completed_task/($completed_task+$yet_to_do);
    }
    public function yearly_completed_task_ratio($user_id){
        $yet_to_do = $this->yet_to_do($user_id);
        $completed_task = self::where('user_id',$user_id)
                            ->pluck('quaterly_complete_task');
        return $completed_task/($completed_task+$yet_to_do);
    }

    public function weekly_incompleted_task_ratio($user_id){
        $yet_to_do = $this->yet_to_do($user_id);
        $completed_task = self::where('user_id',$user_id)
                            ->pluck('weekly_complete_task');
        return $yet_to_do/($completed_task+$yet_to_do);
    }
    public function monthly_incompleted_task_ratio($user_id){
        $yet_to_do = $this->yet_to_do($user_id);
        $completed_task = self::where('user_id',$user_id)
                            ->pluck('monthly_complete_task');
        return $yet_to_do/($completed_task+$yet_to_do);
    }
    public function yearly_incompleted_task_ratio($user_id){
        $yet_to_do = $this->yet_to_do($user_id);
        $completed_task = self::where('user_id',$user_id)
                            ->pluck('quaterly_complete_task');
        return $yet_to_do/($completed_task+$yet_to_do);
    }
}
