<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "header.php";
require_once('stripe/init.php');

\Stripe\Stripe::setApiKey('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');

$token = $_POST['stripeToken'];

$charge = \Stripe\Charge::create([
    'amount' => 1000, // Amount in cents
    'currency' => 'usd',
    'source' => $token,
    'description' => 'Example charge',
]);

echo '<pre>';
print_r($charge);
echo '</pre>';
