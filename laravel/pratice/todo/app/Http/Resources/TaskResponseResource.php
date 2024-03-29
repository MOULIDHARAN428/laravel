<?php

namespace App\Http\Resources;

use App\Traits\formatTime;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResponseResource extends JsonResource
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
        return [
            "id" => $this->id,
            "title" => $this->ttle,
            "description" => $this->description,
            "urgency" => $this->urgency,
            "parent_id" => $this->parent_id,
            "due_time" => $this->formatTime($this->due_time),
        ];
    }
}
