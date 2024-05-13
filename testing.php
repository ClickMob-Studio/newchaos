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
    'Messages' => [
        [
            'From' => [
                'Email' => "admin@chaoscity.co.uk",
                'Name' => "Mailjet Pilot"
            ],
            'To' => [
                [
                    'Email' => "hulladam38@gmail.com",
                    'Name' => "ADAM"
                ]
            ],
            'Subject' => "Your email flight plan!",
            'TextPart' => "Dear passenger 1, welcome to Mailjet! May the delivery force be with you!",
            'HTMLPart' => "<h3>Dear passenger 1, welcome to Mailjet!</h3><br />May the delivery force be with you!"
        ]
    ]
];
$response = $mj->post(Resources::$Email, ['body' => $body]);
$response->success() && var_dump($response->getData());
?>