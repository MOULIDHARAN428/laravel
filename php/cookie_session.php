<?php
    // Cookie - saves on client side : ex: remember password : max size 4kb

    // cookie is the piece of info stored in client browser, used to recognize user and advertising
    // created in server side and stored in client browser
    // stores user session and user preference : name, value, optional attributes, expiration time
    // stores in the form of text file, browser includes the cookies in the header of the resp
    // often used in session managment, user authentication, remembering user preferences
    // cookies are flagged as secure | HttpOnly to enhance security 

    // There are alternative technologies like web storage, session storage, JSON Web Tokens (JWT) to overcome privacy concerns

    setcookie("CookieName","CookieValue", time()+1*60*60,"/path","mydoamin.com",true,true);
    // setcookie(name,value,expiresIn : 1 hr (60 sec* 60min),path,domain,bool secure, bool httponly);

    // $value = isset($_COOKIE["CookieName"]) ? $_COOKIE["CookieName"] : "Cookie not set";
    // $value = $_COOKIE["CookieName"]; //to get the cookie
    // echo $value
    
    
    if(!isset($_COOKIE["user"])) {  
        echo "Sorry, cookie is not found!";  
    } else {  
        echo "Cookie Value: " . $_COOKIE["user"];  
    }
    // output is "Sorrry, Cookie is not found!"
    // When page is loaded, the setcookie is executed on the server in resp header
    // the resp is sent to browser, which stores the cookie locally
    // the isset return false, because the cookie wasn't sent back to the server
    // when the page refresh isset return true

    // delete cookie
    setcookie ("CookieName", "", time() - 3600); //set the cookie expiration date to one hour ago

    // Session - saves on server side

    // It is used to save and pass data form one page to another temporarily
    // creates unique user id for each browser to recognize the user and avoid conflict between multiple browsers
    
    // Random alphanumeric number [identifier][SID] is assigned to user when they visit the webpage
    // This identifier is stored in client side as session cookie
    // Session stores the data of user preferences, authentication status, or any other user-specific information
    
    // When user make req to server, session identifier sent back to server in HTTP req header via session cookie
    // server uses identifier to retrieve session cookie
    // During a users' session, data can be added, modified, or deleted from the session
    // Whenever the user inactive for certain period of time the session gets expired
    // Stored info stored more securely making difficult to tamper
    
    session_start();// function is used to start the session

    $_SESSION["user"] = "Sachin";
    echo $_SESSION["user"];
    
    //stores number of time the session has been used
    if (!isset($_SESSION['counter'])) {  
        $_SESSION['counter'] = 1;  
    } else {  
        $_SESSION['counter']++;  
    }
    unset($_SESSION['user']);// delete specific session
    session_destroy(); // delete all session
    
    // for session it creates cookie id which is stored in client side, typically the session is different from cookie
    // session are closed when ever we closed the tab

    // session vs cookie

    // Cookies are stored in client side, sent or received with each HTTP req and resp
    // Sessions are stored in server side
    // Cookies stores user preferences, authentication tokens, or any other data the website wants to remember.
    // Session data can include any information the server needs to remember about a user during their visit to a website.
    // It is not directly accessible or modifiable by the client.
    // Cookie data have an expiration date, whereas Session expires after an inactive period

    // Sessions are more secure than cookies
    // Cookies have size limitation where as session don't
    // Cookies are manipulated by client side scripting, where as Session data can't


?>