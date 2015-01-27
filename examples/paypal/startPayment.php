<?php

include "../../vendor/autoload.php";
function phpPayAutoloader($class) {
    if(strpos($class, "PhpPay\\") == 0) {
        $class = substr($class, 7);
        $file = "src/$class.php";
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
$returnUrl = $thisUrl."endPayment.php";
$cancelUrl = $thisUrl."cancelPayment.php";
$paypalAdapter = new PayPalAdapter($requester, $returnUrl, $cancelUrl, array(
    "user" => $_REQUEST["user"],
    "pwd" => $_REQUEST["pwd"],
    "signature" => $_REQUEST["signature"]
), "id", true);
$paymentHandler = new PaymentHandler($paypalAdapter);

// With this setup, we can prepare a payment (this will be the same for any adapter)
$order = new Order(10, array(
    array("Price item 1", 120),
    array("Price item 2", 190)
));
$order->addShipping(100);

$paymentId = md5(rand());
$payment = $paymentHandler->preparePayment($paymentId, $order);

//We need to save the payment somewhere locally, to get it back later.
file_put_contents($paymentId.".json", $payment->getSerializedData());

//Now we can use the payment's checkout url to do the checkout
header("Location: ".$payment->getCheckoutUrl());

?>