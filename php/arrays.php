<?php
    // indexed array
    $season=array("summer","winter","spring","autumn"); 
    echo $season[0].PHP_EOL;
    print_r($season);
    
    // Implode : to convert array into the string 
    echo implode (" ",$season).PHP_EOL;  
    // Explode : to convert string into the array
    echo explode (" ","Hey there da!").PHP_EOL;

    // associate array
    $salary=array("Sonoo"=>"350000","John"=>"450000","Kartik"=>"200000");
    $salary["Sonoo"] = "-1";
    foreach($salary as $key => $value) {
        echo $key." ".$value."\n";   
    }
    
    // multidimensional array
    // 1.
    $salary_=array(
            array("Sonoo"=>"350000","John"=>"450000","Kartik"=>"200000"),
            array("Sonoo"=>"350000","John"=>"450000","Kartik"=>"200000")
        ); //2d associate array
    
    // 2.
    $emp = array(  
    array(1,"sonoo",400000),  
    array(2,"john",500000),  
    array(3,"rahul",300000)  
    );  
    echo $emp[0][1].PHP_EOL;

    // Printing the array of array
    foreach($salary_ as $s){
        foreach($s as $key => $value){
            echo $key." ".$value." ";
        }
        echo PHP_EOL;
    }

    echo count($salary_).PHP_EOL;
    print_r($salary); //to print array
    print_r(array_chunk($salary,2)); //to create chunk of data of 2

    sort($season); //returns 1
    print_r($season);

    array_reverse($season);
    array_search("winter",$season); //returns the position of the element
    array_intersect($arr1,$arr2) //returns the intersecting elements


?>