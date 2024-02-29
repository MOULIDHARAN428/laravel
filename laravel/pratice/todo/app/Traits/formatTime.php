<?php
namespace App\Traits;

use Carbon\Carbon;

trait formatTime
{
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

        if ($currentDate > $time) {
            return $daysDifference . ' days ago';
        } 
        else {
            return $daysDifference . ' days remaining';
        }
    }
}