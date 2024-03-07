<?php
namespace App\Traits;

use Carbon\Carbon;

trait formatTime
{
    public function formatTime($time): string
    {
        try {
            // Check if $time is null
            if ($time === null) {
                return 'N/A';  // Or any default value you prefer for null time
            }

            // Type Casting for Carbon 
            if (is_string($time)) {
                $time = Carbon::parse($time);
            }

            // Check if $time is a valid Carbon instance
            if (!$time instanceof Carbon) {
                throw new \InvalidArgumentException('Invalid date/time value provided.');
            }

            $currentDate = Carbon::now();
            $daysDifference = $time->diffInDays($currentDate);

            if ($currentDate > $time) {
                return $daysDifference . ' days ago';
            } else {
                return $daysDifference . ' days remaining';
            }
        } catch (\Exception $e) {
            // Handle the exception gracefully
            return 'Invalid date/time format';
        }
    }
}