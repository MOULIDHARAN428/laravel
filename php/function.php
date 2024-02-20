<?php
    function sayHelloo($name,$age){  
        echo "Hello $name, you are $age years old";  
    }  
    sayHelloo("Sonoo",27);

    # call by reference
    function adder(&$str2)  
    {  
        $str2 .= 'Call By Reference';  
    }  
    $str = 'Hello ';  
    adder($str);  
    echo "\n$str";
    # default arg value : if no arg sent, default arg will be sent by the function itself
    function sayHello($name="Sonoo"){  
        echo "\nHello $name";  
    }  
    sayHello("Rajesh");  
    sayHello();//passing no value  
    sayHello("John");  

    # return value
    function cube($n){  
        return $n*$n*$n;  
    }  
    echo "\nCube of 3 is: ".cube(3); 
    
    # variavle length argument func : can pass greater than or equal to 0 args
    function add(...$numbers) {  
        $sum = 0;  
        foreach ($numbers as $n) {  
            $sum += $n;  
        }  
        return $sum;  
    }
      
    echo add(1, 2, 3, 4);  

    # we can strict declare the function with data types

    function name(int $arg1, int $arg2){
        //
    }

    function nameFunc(int $arg1) : bool{
        return false;
    }

    #recursion 

?>