<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TaskAssigned extends Mailable
{
    use Queueable, SerializesModels;
    public $task, $user, $map;
    /**
     * Create a new message instance.
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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        

        return $this->view('emails.taskAssigned')
                    ->with(['task'=>$this->task,
                            'user'=>$this->user,
                            'map'=>$this->map])
                    ->subject('Task Assigned');
    }
}
