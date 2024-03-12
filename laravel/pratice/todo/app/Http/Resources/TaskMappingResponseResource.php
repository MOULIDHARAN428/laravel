<?php

namespace App\Http\Resources;

use App\Traits\formatTime;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskMappingResponseResource extends JsonResource
{
    use formatTime;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {   
        if(isset($this[0]['name'])){
            return null;
        }
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "task_id" => $this->task_id,
            "task_title" => $this->task_title['title'],
            "role" => $this->role,
            "status" => $this->status,
            "assigned_at" => $this->formatTime($this->assigned_at),
            "time_completed" => $this->formatTime($this->time_completed),
        ];
    }
}
