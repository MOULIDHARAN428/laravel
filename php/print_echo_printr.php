<?php 

    // print  -> can print the array, obj : string output
    // printr -> can't have variable within the quotes : kinda associate | hash map
    // echo   -> can have variable within the quotes : string output

    // variables are case sensitive but the inbuilt functions are not case sensitive-
    $var = "black";
    echo $var;
    echo "Color is : $var";
    Echo "\nhey";
    ECHO "\nhey
    ther it \twill
    be multi line\n";
    echo "hey \"quotes\" \n";

    print_r("hey ");
    print "hey";

    // echo is faster than print

    //in echo we can print variables but in print we can't have multiple variables
    $fname = "Mouli";
    $sname = "dharan";
    echo "My name is :".$fname,$sname."\n";

    // print returns 1, echo doesn't returns anything
?>