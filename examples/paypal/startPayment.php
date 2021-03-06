<?php

use PhpPay\Requester;
use PhpPay\Order;
use PhpPay\PayPal\PayPalAdapter;
use PhpPay\PaymentHandler;

include "../../vendor/autoload.php";
function phpPayAutoloader($class) {
    if(strpos($class, "PhpPay\\") == 0) {
        $class = str_replace("\\", "/", substr($class, 7));
        $file = "../../src/$class.php";
        if(file_exists($file)) {
            include $file;
        }
    }
}
spl_autoload_register("phpPayAutoloader");

$thisUrl = $_REQUEST["thisUrl"];

// Our simple little requester
$requester = new Requester();

// This is the paypal-specific setup part
$returnUrl = $thisUrl."endPayment.php?id=".md5(rand());
$cancelUrl = $thisUrl."cancelPayment.php?id=".md5(rand());
$paypalAdapter = new PayPalAdapter($requester, $returnUrl, $cancelUrl, array(
    "user" => $_REQUEST["user"],
    "pwd" => $_REQUEST["pwd"],
    "signature" => $_REQUEST["signature"]
), true);
$paymentHandler = new PaymentHandler($paypalAdapter);

// With this setup, we can prepare a payment (this will be the same for any adapter)
$order = new Order(10, array(
    array("Price item 1", 120),
    array("Price item 2", 190)
));
$order->addShipping(100);

$payment = $paymentHandler->preparePayment($order);

//We need to save the payment somewhere locally, to get it back later.
file_put_contents($paymentId.".json", $payment->getSerializedData());

//Now we can use the payment's checkout url to do the checkout
header("Location: ".$payment->getCheckoutUrl());

?>