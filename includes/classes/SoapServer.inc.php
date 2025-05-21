<?php

require_once 'nusoap/lib/nusoap.php';
$server = new soap_server();
$server->register('query');
function Autentication($key)
{
    if ($key != '15252185521452145514545214dswqjghwqgd wqqwds c12sa12as1d2asd2ad54a2d1as2d1as21da21das21') {
        return false;
    }

    return true;
}

function query($key, $code)
{
    if (!Autentication($key)) {
        return;
    }
    $rs = DBi::$conn->query($code);
    $array = [];
    while ($res = mysqli_fetch_object($rs)) {
        $array[] = $res;
    }

    return json_encode($array);
}
$server->register('object');
function object($key, $name, $code)
{
    if (!Autentication($key)) {
        return;
    }
    $object = new $name($code);

    return json_encode(new $name($code));
}
$server->register('SendPM');
function SendPM($key, $to, $from, $subject, $msgtext)
{
    if (!Autentication($key)) {
        return;
    }
    Pms::Add($to, $from, time(), $subject, $msgtext);
}

// Use the request to (try to) invoke the service
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
