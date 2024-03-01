<?php
namespace App\Traits;

use App\Jobs\SendTaskAssignEmail;
use App\Jobs\SendTaskAssignNotificationEmail;
use App\Jobs\SendTaskDeleteEmail;
use App\Jobs\SendTaskEditEmail;
use App\Task;
use App\User;
use App\TaskMapping;
use Illuminate\Support\Facades\Auth;

trait sendMailTrait
{
    public $map_data, $user_data, $task_data;
    public function assignVariables($id_data){
        $auth_user = Auth::user();
        $this->map_data = TaskMapping::find($id_data['id']);
        $this->user_data = User::where('id',$id_data['user_id'])->get(['name','email'])->first();
        $this->user_data['auth_user_mail'] = $auth_user->email; 
        $this->task_data = Task::where('id',$id_data['task_id'])->get(['title','due_time'])->first();
    }
    //needs to send mail to the auth user also :)
    public function sendAssignMail($id_data){
        $this->assignVariables($id_data);
        SendTaskAssignEmail::dispatch($this->user_data->toArray(),$this->task_data->toArray(),$this->map_data->toArray())->onConnection('database');
    }
    public function sendAssignNotificationMail($user_id){
        $this->user_data = User::where('id',$user_id)->get(['name','email'])->first();
        SendTaskAssignNotificationEmail::dispatch($this->user_data->toArray())->onConnection('database');
    }

    public function sendEditMail($id_data){
        $this->assignVariables($id_data);
        SendTaskEditEmail::dispatch($this->user_data->toArray(), $this->task_data->toArray(), $this->map_data->toArray())->onConnection('database');
    }

    public function sendDeleteMail($id_data){
        $this->assignVariables($id_data);

        if(!isset($id_data['user_change'])){
            TaskMapping::where('id', $id_data['id'])
                ->delete();
        }
        SendTaskDeleteEmail::dispatch($this->user_data->toArray(),$this->task_data->toArray(),$this->map_data->toArray())->onConnection('database');

    }
}