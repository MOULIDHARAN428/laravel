<?php 
    // variables are case sensitive but the inbuilt functions are not case sensitive-
    // in php, variables will be type casted to the correct data type

    //static variable, global variable, local variable

    $var = "black";
    $a = $aa = $aaa = 5;
    $b = 5.5;
    $c = true;
    $d = False; // doesn't print 
    $e = false; // doesn't print
    echo $a."\n".$b."\n".$c."\n".$e."\n";
    echo "Color is : $var";
    Echo "\nhey";
    ECHO "\nhey
    this is
    multi line\tcomments\n";
    echo "hey \"quotes\"";
    // echo $not; Error : undefined variable
    // code below works fine
    echo "\nheyyyyy";

    // we can't refer global variable without using globals key word
    function add_global_var(){
        // $sum = $a + $b; -> error
        static $s1 = 1;
        $s2 = 1; 
        $sum = $GLOBALS['a']+$GLOBALS['b'];
        echo "\n".$sum."\n";
        $s1++;
        $s2++;
        echo $s1." ".$s2;        
    }
    add_global_var();

    add_global_var();
?>