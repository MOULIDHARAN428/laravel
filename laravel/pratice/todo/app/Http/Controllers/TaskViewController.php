<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskViewController extends Controller
{
    public function tasks(){
        return view('task.home');
    }

}
