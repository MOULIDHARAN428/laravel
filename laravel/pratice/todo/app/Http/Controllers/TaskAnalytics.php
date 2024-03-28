<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class TaskAnalytics extends Controller
{
    public function analyticsDayData(Request $request){
        $validator = FacadesValidator::make($request->all(), [
            'from' => 'nullable|date_format:Y-m-d H:i:s',
            'to' => 'nullable|date_format:Y-m-d H:i:s',
        ]);
        if ($validator->fails() || (isset($request['from']) && isset($request['to']) && $request['from']>$request['to'])){
            return response()->json([
                'ok' => false,
                'error' => "The from date should be smaller than to date" 
            ], 422);
        }

        $data = Task::analyticsDayData($request);

        return response()->json([
            'ok' => true,
            'data' => $data 
        ], 200);

    }

    public function analyticsHourData(Request $request){
        $validator = FacadesValidator::make($request->all(), [
            'from' => 'nullable|date_format:Y-m-d H:i:s',
            'to' => 'nullable|date_format:Y-m-d H:i:s',
        ]);
        if ($validator->fails() || (isset($request['from']) && isset($request['to']) && $request['from']>$request['to'])){
            return response()->json([
                'ok' => false,
                'error' => "The from date should be smaller than to date" 
            ], 422);
        }

        $data = Task::analyticsHourData($request);

        return response()->json([
            'ok' => true,
            'data' => $data 
        ], 200);

    }

    public function analyticsTaskAssignes(Request $request){
        $validator = FacadesValidator::make($request->all(), [
            'from' => 'nullable|date_format:Y-m-d H:i:s',
            'to' => 'nullable|date_format:Y-m-d H:i:s',
        ]);
        if ($validator->fails() || (isset($request['from']) && isset($request['to']) && $request['from']>$request['to'])){
            return response()->json([
                'ok' => false,
                'error' => "The from date should be smaller than to date" 
            ], 422);
        }

        $data = Task::analyticsTaskAssignes($request);

        return response()->json([
            'ok' => true,
            'data' => $data 
        ], 200);

    }
    
    public function analyticUserTasks(){
        $data = Task::analyticUserTasks();

        return response()->json([
            'ok' => true,
            'data' => $data 
        ], 200);
    }
}
