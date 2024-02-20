<?php
    $val = 10;
    //if-else
    if($val>10){}
    elseif($val>5){}
    else{}
    //nested if
    if($val>10){
        if($val>20){

        }
    }

    //switch | nested switch also possible
    // if any of the case satisfies then every cases below 2 executes until break statement occurs
    switch($val){
        case 1:
            //statement
            break;
        case 2:
            break;
        default:
            echo "print if no cases satisfies";
            break;
    }


?>