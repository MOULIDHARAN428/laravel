<?php

    var_dump($str); // returns the datatype
    // 1. Scalar
    // boolean
    $var = TRUE;
    // int - can be decimal, octal, hexadecimal
    $var = 1; $var = 02; $var = 0xaf4;
    echo $var."\n";
    // float
    $var = 19.32;
    // str
    $var = "str";


    // 2. Compound 
    // Array
    $arr = array(1,2,3,4);
    $arr = array("arr",4,1.0,true,null); // can contains different data types
    echo $arr[0]."\n";
    
    // Object
    class bike{
        function model(){
            echo "Bike Model";
            return 0;
        }
    }
    $obj = new bike();
    $objModel = $obj->model();
    echo $objModel;

    // 3. Special
    // Null
    $nl = null;

    // Resource
    // Adv topics
?>