<?php 
    // gmp -> GNU Multiple Precision
    // provides set of func for performing arithmetic operations on large integers
   
    // Creating GMP numbers
    $a = gmp_init(123); // Integer
    $b = gmp_init("456"); // String

    // Addition
    $result_add = gmp_add($a, $b);
    echo gmp_strval($result_add) . PHP_EOL; // Output: 579

    // Subtraction
    $result_sub = gmp_sub($b, $a);
    echo gmp_strval($result_sub) . PHP_EOL; // Output: 333

    // Multiplication
    $result_mul = gmp_mul($a, $b);
    echo gmp_strval($result_mul) . PHP_EOL; // Output: 56088

    // Division
    $result_div = gmp_div($b, $a);
    echo gmp_strval($result_div) . PHP_EOL; // Output: 3

    // Modulo
    $result_mod = gmp_mod($b, $a);
    echo gmp_strval($result_mod) . PHP_EOL; // Output: 87
?>