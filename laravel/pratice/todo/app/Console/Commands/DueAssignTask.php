<?php

namespace App\Console\Commands;

use App\Task;
use App\TaskMapping;
use App\UserTaskAnalytic;
use Carbon\Carbon;
use Illuminate\Console\Command;


class DueAssignTask extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Incrementing the due task in userTaskAnalytic everyday whenever the users exceeds the deadline';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //we need to have the duplicated of the userID

        $currentTime = Carbon::now();
        $tasks = Task::where('due_time','<',$currentTime)->get('id');

        $usersID = TaskMapping::where('status','0')
                                ->whereIn('task_id',$tasks)
                                ->pluck('user_id');

        foreach($usersID as $userID){
            UserTaskAnalytic::where('user_id',$userID)
                                ->increment('due_task');
        }

        // this will remove the duplicates of the userID

        // TaskMapping::where('status', '0')
        // ->groupBy('user_id')
        // ->pluck('user_id')
        // ->each(function ($userID) {
        //     UserTaskAnalytic::where('user_id', $userID)
        //         ->increment('due_task');
        // });
    }
}
