<?php 
    // can be used without the braces too
    for( $i = 0; $i < 10; $i++ ){
        echo $i." ";
    }
    // for(;;); inifinte loop

    // for each loop
    $season = array("summer","winter","spring","autumn");  
    foreach( $season as $arr ){  
        echo "$arr ";  
    }

    //map
    $employee = array (  
        "Name" => "Alex",  
        "Email" => "alex_jtp@gmail.com",  
        "Age" => 21,  
        "Gender" => "Male"  
    ); 
    //array of map : array(array(map),array(map));
      
    //display associative array element through foreach loop  
    foreach ($employee as $key => $element) {  
        echo $key . " : " . $element." \n";
    }
    $val=0;
    //while loop
    while($val<10){
        $val++;
    }

    while($val<20):
    $val++;
    endwhile;


    //do while
    do{
        $val++;
    }while($val< 10);


?>