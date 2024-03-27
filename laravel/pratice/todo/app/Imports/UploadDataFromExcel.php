<?php

namespace App\Imports;

use App\Task;
use App\TaskMapping;
use App\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UploadDataFromExcel implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $skip = true;
        foreach($rows as $row){
            if($skip){
                $skip = false;
                continue;
            }
            if(isset($row[5])){
                $task_title = $row[5];
                $task_id = Task::where('id',$task_title)->value('id');

                //creating the sub task
                $sub_task = new Task();
                $sub_task->title = $row[2];
                $sub_task->description = $row[3];
                $sub_task->urgency = $row[4];
                $sub_task->due_time = $row[7];
                $sub_task->parent_id = $task_id;
                $sub_task->save();

                //assign user
                $user_id = User::where('email',$row[8])->value('id');
                $task_mapping = new TaskMapping();
                $task_mapping->user_id = $user_id;
                $task_mapping->task_id = $task_id;
                $task_mapping->role = $row[9];
                $task_mapping->assigned_at = $row[10];
                $task_mapping->save();


            }//create sub-task and assign user
            else if(isset($row[6])){
                $task_title = $row[6];
                $task_id = Task::where('id',$task_title)->value('id');

                //assign user
                $user_id = User::where('email',$row[8])->value('id');
                $task_mapping = new TaskMapping();
                $task_mapping->user_id = $user_id;
                $task_mapping->task_id = $task_id;
                $task_mapping->role = $row[9];
                $task_mapping->assigned_at = $row[10];
                $task_mapping->save();

            }//assign user
            else{
                //creating the task
                $task = new Task();
                $task->title = $row[2];
                $task->description = $row[3];
                $task->urgency = $row[4];
                $task->due_time = $row[7];
                $task->save();

                //assign the user
                $task_id = Task::where('title',$row[2])->value('id');
                $user_id = User::where('email',$row[8])->value('id');
                $task_mapping = new TaskMapping();
                $task_mapping->user_id = $user_id;
                $task_mapping->task_id = $task_id;
                $task_mapping->role = $row[9];
                $task_mapping->assigned_at = $row[10];
                $task_mapping->save();


            }//create task and assign user
        }
    }
}
