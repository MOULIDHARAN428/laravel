<?php
    $date = "2019-02-26";
    $newDate = date("m-d-Y", strtotime($date));  
    echo "New date format is: ".$newDate. " (MM-DD-YYYY)".PHP_EOL;
    $newDate = date("d-m-Y", strtotime($date));  
    echo "New date format is: ".$newDate. " (MM-DD-YYYY)".PHP_EOL;  

    $date = "01-02-2002";
    $newDate = date("m-d-y", strtotime($date));
    echo "".$newDate. "".PHP_EOL;
    $newDate = date("d-m-y", strtotime($date));
    echo "".$newDate. "".PHP_EOL;

?>