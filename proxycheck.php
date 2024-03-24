<?php
function ipProxyPortCheck($ip) {
//timeout you want to use to test
    $timeout = 5;
// ports we're going to check
    $ports = array(80, 3128, 8080);
// flag to be returned, 0 means safe, 1 means proxy
    $flag = 0;
// loop through each of the ports we're checking
    foreach ($ports as $port) {
// this is the code that does the actual checking for the port
        @$fp = fsockopen($ip, $port, $errno, $errstr, $timeout);
// test if something was returned, ie the port is open
        if (!empty($fp)) {
// we know the set the flag
            $flag = 1;
// close our connection to the IP
            fclose($fp);
        }
    }
// send our flag back to the calling code
    return $flag;
}
// call our function and check the IP in there
echo ipProxyPortCheck(86.134.251.64);
echo ipProxyPortCheck(206.116.178.113);
echo ipProxyPortCheck(71.226.28.5);
?>