<?php
    $n1 = 10;
    $n2 = "10";
    $n3 = $n1 + $n2; // PHP will automatically convert $n2 to an integer
    echo "".$n3;

    // explicit typecasting
    $integerNumber = (int)$number; // Convert to integer
    $floatNumber = (float)$number; // Convert to float
    $stringNumber = (string)$integerNumber; // Convert to string

    // set type func
    $number = "123";
    settype($number, "int"); // Convert to integer

    // type casting function
    $number = "123";
    $integerNumber = intval($number); // Convert to integer
    $floatNumber = floatval($number); // Convert to float
    $stringNumber = strval($integerNumber); // Convert to string

    // array and obj type casting
    $arrayVar = (array)$someVariable; // Convert to array
    $objectVar = (object)$someVariable; // Convert to object

    // boolean type casting
    $result = (bool)$someValue; // Convert to boolean

?>