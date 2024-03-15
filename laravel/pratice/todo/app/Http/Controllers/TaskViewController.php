<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskViewController extends Controller
{
    public function tasks(){
        return view('task.home');
    }
    public function specificTask($task_id){
        return view('task.mapping',['task_id'=>$task_id]);
    }
    public function specificParentTask($task_id){
        return view('task.subtaskmapping',['task_id'=>$task_id]);
    }
    public function profile(){
        return view('profile.home');
    }
}
