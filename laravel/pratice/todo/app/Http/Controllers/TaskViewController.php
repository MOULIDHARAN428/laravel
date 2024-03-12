<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskViewController extends Controller
{
    public function tasks(){
        return view('task.home');
    }
    public function specific_task($task_id){
        return view('task.mapping',['task_id'=>$task_id]);
    }
}
