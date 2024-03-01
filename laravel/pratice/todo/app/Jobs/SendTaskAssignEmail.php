<?php

namespace App\Jobs;

use App\Mail\TaskAssigned;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendTaskAssignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 3;  
    protected $task, $user, $map;
    /** 
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $task, $map)
    {
        $this->task = $task;
        $this->user = $user;
        $this->map  = $map;
        // dd($user, $task, $map);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user['email'])
        ->bcc($this->user['auth_user_mail'])
        ->send(new TaskAssigned($this->user,$this->task,$this->map));
    }
}
