<?php
    $str = "hello";
    $str .= " Jhon!";
    $str[0] = 'H';
    echo $str.PHP_EOL;
    $str = 'hey \'there\\\\\' hello'.PHP_EOL;
    // use backslash for using quotes and backslash
    echo $str;
    $str = "\n'hey \nthere' \\\n";

    // double quotes can have special characters whereas the single quotes can't have special characters

    echo '$str'; // prints the variable name
    echo "$str"; // prints the value
    $str = <<<Demo
    I can write any thing that save in str until the demo is closed
    Demo;
    echo $str.PHP_EOL;
    echo<<<demo
    Multi
    Line
    printing
    demo;
    
    // add back
    echo addcslashes("\nheyy\n","y"); // he\y\y
    // echo addslashes("heyy\n");
    // strlen($str);
    echo "".chr(97).PHP_EOL;
    echo "".chr(65).PHP_EOL;
    
?>