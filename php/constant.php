<?php
    define("message","heyy!");
    // echo Message; have compilation errors
    
    // define("MESSAGE","hello",true); not working from 7.3 version where true|false for case sensitive
    // const can't be declared inside function, loop, conditional statements but define can be declared there

    // const are global and can be used across the script
    // const can be accessed outside the class using class_name::const_name
    //To access const inside the class use self::cons_name

    $Message = "hey";
    const msg = "hi";
    echo $Message." ".message." ".msg." ";
?>