<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public function toArray($request)
    {
        if($this->resource){ //status
            return [
                'data' => [
                    $this->resource
                ]
            ];
        }
        else{
            return [
                'data' => [
                    "No data"
                ]
            ];
        }
    }
}
