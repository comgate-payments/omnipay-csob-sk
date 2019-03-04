<?php

use Omnipay\Omnipay;

require_once __DIR__ . '/vendor/autoload.php';


$gateway = Omnipay::create('Eplatby');

$gateway->setMid('11111111');
$gateway->setSharedSecret('1111111111111111111111111111111111111111111111111111111111111111');

$gateway->setTestMode(true);

$response = $gateway->completePurchase([
	'amount' => '10.00',
	'currency' => 'EUR',
	'VS' => '123456',
	'CS' => '0321',
	'rurl' => 'http://localhost:4444/testserver.php',
])->send();


if ($response->isSuccessful()) {
    
    // Payment was successful
    echo "OK - {$response->getVs()}";

} elseif ($response->isRedirect()) {
    
    // Redirect to offsite payment gateway
    echo($response->getRedirectUrl() . "\n");
    //$response->redirect();

} else {
	echo "FAIL!";
    // Payment failed
    echo $response->getMessage();
}


