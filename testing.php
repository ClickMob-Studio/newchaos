<?php
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
require 'vendor/autoload.php';

use \Mailjet\Resources;


 $apikey = '7dc2ad83e7f15563b1dee7d48109dbb7';
 $apisecret = '15326068ed7ef53039e03ca05662bde2';
$mj = new \Mailjet\Client($apikey, $apisecret);

$body = [
    'FromEmail' => "pilot@mailjet.com",
    'FromName' => "Mailjet Pilot",
    'Subject' => "Your email flight plan!",
    'Text-part' => "Dear passenger, welcome to Mailjet! May the delivery force be with you!",
    'Html-part' => "<h3>Dear passenger, welcome to <a href=\"https://www.mailjet.com/\">Mailjet</a>!<br />May the delivery force be with you!",
    'Recipients' => [
        [
            'Email' => "passenger@mailjet.com"
        ]
    ]
];
$response = $mj->post(Resources::$Email, ['body' => $body]);
$response->success() && var_dump($response->getData());