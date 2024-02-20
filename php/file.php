<?php

    $filename = "c:\\myfile.txt";  

    //open file
    $handle = fopen($filename, "r");
    // r r+ w w+ a a+ 
    // for append and write, if file not exits it creates the new one
    fclose($handle);

    //read file
    $contents = fread($handle,filesize($filename));
    echo $contents;

    $contents = fgets($handle); // to read single line
    echo $contents; 

    $contents = fgetc($handle); // to read single character
    echo $contents;

    //write file
    $fp = fopen('data.txt', 'w');//opens file in write-only mode  
    fwrite($fp, 'welcome ');  
    fwrite($fp, 'to php file write');  
    fclose($fp);
    // welcome to php file write

    //append file
    $fp = fopen('data.txt', 'a');//opens file in append mode  
    fwrite($fp, ' this is additional text ');  
    fwrite($fp, 'appending data');  
    fclose($fp);
    // welcome to php file write this is additional text appending data

    //delete file
    $status=unlink('data.txt');
    echo $status; //prints true;
?>