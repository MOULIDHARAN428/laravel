<?php 
    # include and require adv : code reusablility, easy editable
    
    # include - warning
    include("file.html"); // continues execution if the file.html not exists also

    # require - compile error
    require("file.html"); // stops excecution if file.html not exists

    # require_once : the file should shoud be required once
    # include_once : the file should should be included once
    

?>