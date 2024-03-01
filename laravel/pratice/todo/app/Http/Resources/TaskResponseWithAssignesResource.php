<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResponseWithAssignesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

     public function formatTime($time) : string{
        if ($time === null) {
            return 'N/A';  // Or any default value you prefer for null time
        }
        
        // Type Casting for carbon 
        if (is_string($time)) {
            $time = Carbon::parse($time);
        }

        $currentDate = Carbon::now();
        $daysDifference = $time->diffInDays($currentDate);

        if ($daysDifference == 0){
            return "Today";
        }
        else if ($currentDate > $time) {
            return $daysDifference . ' days ago';
        } 
        else {
            return $daysDifference . ' days remaining';
        }
    }
    public function toArray($request)
    {
        $assignes = null;
        if (isset($this['taskMappings'])) {
            $assignes = $this['taskMappings']->map(function ($assign) {
                return [
                    "id" => $assign['id'],
                    "user_id" => $assign['user_id'],
                    "role" => $assign['role'],
                    "status" => $assign['status'],
                    "assigned_at" => $this->formatTime($assign['assigned_at']),
                    "time_completed" => $this->formatTime($assign['time_completed']),
                ];
            })->toArray();
        }
        return [
            "id" => $this['id'],
            "title" => $this['title'],
            "description" => $this['description'],
            "urgency" => $this['urgency'],
            "status" => $this['status'],
            "parent_id" => $this['parent_id'] ?? '',
            "due_time" => $this->formatTime($this['due_time']),
            "time_completed" => $this->formatTime($this['time_completed']),
            "assignes" => $assignes,
        ];
        
    }
    

}
