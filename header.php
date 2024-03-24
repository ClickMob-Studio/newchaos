<?php
error_reporting(0);
$useMobileHeader = isset($_COOKIE['useMobileHeader']) ? $_COOKIE['useMobileHeader'] : false;


if(stristr($_SERVER['HTTP_USER_AGENT'], "Mobile")){
    if(isset($_COOKIE['useMobileHeader'])){ // if mobile browser
    require_once "header_m.php";
    }else{
        require_once "headertest.php";
}
}
else { // desktop browser

require_once "headertest.php";
}
