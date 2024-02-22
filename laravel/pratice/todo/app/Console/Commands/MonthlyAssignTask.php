<?php

namespace App\Console\Commands;

use App\UserTaskAnalytic;
use Illuminate\Console\Command;

class MonthlyAssignTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:monthlyUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign monthly_completed_task to zero whenever the month ends';

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
        UserTaskAnalytic::where([])
                        ->update(['monthly_complete_task'=>0]);
    }
}
