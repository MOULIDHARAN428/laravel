<?php 
    $arr1 = array(1,2,3); 
    print_r($arr1); // assigns the index on it's own

    $arr2 = array('firstName' => 'Rahul', 'lastName' => 'Kumar', 'email' => 'rahul@gmail.com');
    print_r($arr2); 
    $json = json_encode($arr2);
    echo $json."\n";

    //both are same
    var_dump(json_decode($json, true));
    var_dump($arr2);
?>