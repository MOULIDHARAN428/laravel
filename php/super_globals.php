<?php

    // $GLOBALS - ref to the variable that declared outside the func or loop or conditional statements
    $x = 75;
    function myfunction(){
        echo $GLOBALS['x'];
    }
    myfunction();

    // $_SERVER is a PHP super global variable which holds information about headers, paths, and script locations.
    echo $_SERVER['PHP_SELF'];
    echo "\n";
    echo $_SERVER['SERVER_NAME'];
    echo "\n";
    echo $_SERVER['HTTP_HOST'];
    echo "\n";
    echo $_SERVER['HTTP_REFERER'];
    echo "\n";
    echo $_SERVER['HTTP_USER_AGENT'];
    echo "\n";
    $_SERVER['HTTP_HOST'];
    
    // $_REQUEST
    // $_REQUEST is an array containing data from $_GET, $_POST, and $_COOKIE
    $name = $_REQUEST['fname'];
    echo $name;

    $_POST['name'];
    $_GET['name'];

?>