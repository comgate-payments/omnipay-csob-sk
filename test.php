<?php

use Omnipay\Omnipay;

require_once __DIR__ . '/vendor/autoload.php';

$gateway = Omnipay::create('CsobSk');

$gateway->setMid('11111111');
// $gateway->setSharedSecret('11111111');
$gateway->setSharedSecret('1111111111111111111111111111111111111111111111111111111111111111');
// $gateway->setSharedSecret('11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111');
// $gateway->setMid('testHMAC');
// $gateway->setSharedSecret('36fmtvHtjyRAieZ9k+x9zldZIYhSx/vh22iaBG9n1aOd1ncFKrUCeR7gAE5Dg2D1');

$gateway->setTestMode(true);

$response = $gateway->purchase([
    'amount' => '10.00',
    'VS' => '123456',
    'CS' => '0321',
    'rurl' => 'http://localhost:4444/testserver.php',
])->send();

if ($response->isSuccessful()) {
    
    // Payment was successful
    print_r($response);

} elseif ($response->isRedirect()) {
    
    // Redirect to offsite payment gateway
    // echo($response->getRedirectUrl() . "\n");

    $response->redirect();

} else {

    // Payment failed
    echo $response->getMessage();
}


