<?php 
    $str= "javatpoint";  
    //Encryption

    //one_way encryption : can't be decode, only we can encode
    echo md5($str);  
    
    //two_way encrytion : we can bot decode and encode

    $str1= base64_encode($str);  
    echo "->".$str1.PHP_EOL;
    $str1 = base64_decode($str1);
    echo $str1.PHP_EOL;
    
    $str1 = base64_decode("javatpoint");
    echo $str1.PHP_EOL;
    $str1 = base64_encode("��ڶ��");
    echo $str1.PHP_EOL;

    // SSL Encrypt

    $str= "Welcome";
    $ciphering_value = "AES-128-CTR";
    $encryption_key = "mouli";  
    // Generate a random IV
    $iv_length = openssl_cipher_iv_length($ciphering_value);
    $iv = openssl_random_pseudo_bytes($iv_length);

    $en = openssl_encrypt($str,$ciphering_value,$encryption_key,0,$iv);
    $de = openssl_decrypt($en,$ciphering_value,$encryption_key,0,$iv);
    echo "->".$en." ".$de.PHP_EOL;

    // url encoder
    echo urlencode("https://www.javatpoint.com/");  
?>