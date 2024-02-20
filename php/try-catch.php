<?php
    try{
        $x = 5/0;
    }
    catch(Exception $e){
        echo "".$e->getMessage()."";
    }
    finally{
        echo "Finally...";
    }

    if(true)
        throw new Exception("It's the throw!"); // uncaught exception

     
?>