<?php
    $state = "UP";
    $$state = "Lucknow";
    // echo "Captial of $state is $$captial";
    echo "Captial of $state is ".$$state."\n";

    $x = "abc";
    $$x = "def";
    echo $x." ";
    echo $$x." "; 
    echo $abc." ";
    // can have any number of $ 
    $$$x = 11;
    echo $def;
?>