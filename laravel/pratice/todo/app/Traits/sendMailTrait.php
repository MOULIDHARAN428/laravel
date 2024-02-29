<?php
namespace App\Traits;

use App\Jobs\SendTaskAssignEmail;
use App\Jobs\SendTaskAssignNotificationEmail;
use App\Jobs\SendTaskDeleteEmail;
use App\Jobs\SendTaskEditEmail;
use App\User;
use App\TaskMapping;

trait sendMailTrait
{
    public $map_data, $user_data, $task_data;
    public function assignVariables($id_data){
        
        $this->map_data = TaskMapping::find($id_data['id']);
        $this->user_data = User::where('id',$id_data['user_id'])->get(['name','email'])->first();
        $this->task_data = TaskMapping::where('id',$id_data['task_id'])->get(['title','due_time'])->first();
    }
    //needs to send mail to the auth user also :)
    public function sendAssignMail($id_data){
        $this->assignVariables($id_data);
        SendTaskAssignEmail::dispatch($this->user_data->toArray(),$this->task_data->toArray(),$this->map_data->toArray())->onQueue('emails');
    }
    public function sendAssignNotificationMail($user_id){
        $this->user_data = User::where('id',$user_id)->get(['name','email'])->first();
        SendTaskAssignNotificationEmail::dispatch($this->user_data->toArray())->onQueue('emails');
    }

    public function sendEditMail($id_data){
        $this->assignVariables($id_data);
        SendTaskEditEmail::dispatch($this->user_data->toArray(), $this->task_data->toArray(), $this->map_data->toArray())->onQueue('emails');
    }

    public function sendDeleteMail($id_data){
        $this->assignVariables($id_data);
        SendTaskDeleteEmail::dispatch($this->user_data->toArray(),$this->task_data->toArray(),$this->map_data->toArray())->onQueue('emails');
    }
}