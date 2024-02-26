<?php

namespace App\Jobs;

use App\Mail\TaskEdited;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendTaskEditEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $task, $user, $map;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user,$task,$map)
    {
        $this->task = $task;
        $this->user = $user;
        $this->map  = $map;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user['email'])->send(new TaskEdited($this->user,$this->task,$this->map));
    }
}
