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
    public function yetToDoTask($user_id){
        
        $yet_to_do = self::where('user_id',$user_id)
                          ->pluck('yet_to_do_task');
        return $yet_to_do;

    }
    public function dueTask($user_id){
        
        $yet_to_do = self::where('user_id',$user_id)
                          ->pluck('due_task');
        return $yet_to_do;

    }
    public function completedTask($user_id){

        $yet_to_do = self::where('user_id',$user_id)
                          ->pluck('completed_task');
        return $yet_to_do;

    }
    public function weeklyCompletedTaskRatio($user_id){

        $yet_to_do = $this->yetToDoTask($user_id);
        $completed_task = self::where('user_id',$user_id)
                            ->pluck('weekly_complete_task');
        return $completed_task/($completed_task+$yet_to_do);

    }
    public function monthlyCompletedTaskRatio($user_id){

        $yet_to_do = $this->yetToDoTask($user_id);
        $completed_task = self::where('user_id',$user_id)
                            ->pluck('monthly_complete_task');
        return $completed_task/($completed_task+$yet_to_do);

    }
    public function yearlyCompletedTaskRatio($user_id){

        $yet_to_do = $this->yetToDoTask($user_id);
        $completed_task = self::where('user_id',$user_id)
                            ->pluck('quaterly_complete_task');
        return $completed_task/($completed_task+$yet_to_do);

    }

    public function weeklyIncompletedTaskRatio($user_id){

        $yet_to_do = $this->yetToDoTask($user_id);
        $completed_task = self::where('user_id',$user_id)
                            ->pluck('weekly_complete_task');
        return $yet_to_do/($completed_task+$yet_to_do);

    }
    public function monthlyIncompletedTaskRatio($user_id){

        $yet_to_do = $this->yetToDoTask($user_id);
        $completed_task = self::where('user_id',$user_id)
                            ->pluck('monthly_complete_task');
        return $yet_to_do/($completed_task+$yet_to_do);

    }
    public function yearlyIncompletedTaskRatio($user_id){

        $yet_to_do = $this->yetToDoTask($user_id);
        $completed_task = self::where('user_id',$user_id)
                            ->pluck('quaterly_complete_task');
        return $yet_to_do/($completed_task+$yet_to_do);

    }
}
