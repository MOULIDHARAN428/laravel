<?php

namespace App\Imports;

use App\Task;
use App\TaskMapping;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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

            if($row[6]!=""){
                $decimalNumber = $row[6];
                $secondsSinceEpoch = ($decimalNumber - 25569) * 86400;
                $dateTime = new \DateTime("@$secondsSinceEpoch");
                $row[6] = $dateTime->format('Y-m-d H:i:s');
            }
            if($row[9]!=""){
                $decimalNumber = $row[9];
                $secondsSinceEpoch = ($decimalNumber - 25569) * 86400;
                $dateTime = new \DateTime("@$secondsSinceEpoch");
                $row[9] = $dateTime->format('Y-m-d H:i:s');
            }
            

            if($row[4]!=""){

                $task_title = $row[4];
                $task_id = Task::where('title',$task_title)->value('id');

                //creating the sub task
                $sub_task = new Task();
                $sub_task->title = $row[1];
                $sub_task->description = $row[2];
                $sub_task->urgency = $row[3];
                $sub_task->due_time = $row[6];
                $sub_task->parent_id = $task_id;
                $sub_task->save();

                //assign user
                $user_id = User::where('email',$row[7])->value('id');
                $task_mapping = new TaskMapping();
                $task_mapping->user_id = $user_id;
                $task_mapping->task_id = $task_id;
                $task_mapping->role = $row[8];
                $task_mapping->assigned_at = $row[9];
                $task_mapping->save();

            }//create sub-task and assign user

            else if($row[5]!=""){
                $task_title = $row[5];
                $task_id = Task::where('title',$task_title)->value('id');
                //assign user
                $user_id = User::where('email',$row[7])->value('id');
                $task_mapping = new TaskMapping();
                $task_mapping->user_id = $user_id;
                $task_mapping->task_id = $task_id;
                $task_mapping->role = $row[8];
                $task_mapping->assigned_at = $row[9];
                $task_mapping->save();
            }//assign user

            else{
                //creating the task
                $task = new Task();
                $task->title = $row[1];
                $task->description = $row[2];
                $task->urgency = $row[3];
                $task->due_time = $row[6];
                $task->save();

                if($row[7]!=""){
                    //assign the user
                    $task_id = Task::where('title',$row[1])->value('id');
                    $user_id = User::where('email',$row[7])->value('id');
                    $task_mapping = new TaskMapping();
                    $task_mapping->user_id = $user_id;
                    $task_mapping->task_id = $task_id;
                    $task_mapping->role = $row[8];
                    $task_mapping->assigned_at = $row[9];
                    $task_mapping->save();
                }

            }//create task and assign user


        }
        Log::info("done");
    }
}
