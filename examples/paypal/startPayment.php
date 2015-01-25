<?php

$thisUrl = $_REQUEST["thisUrl"];

// Our simple little requester
$requester = new Requester();

// This is the paypal-specific setup part
$returnUrl = $thisUrl."endPayment.php";
$cancelUrl = $thisUrl."cancelPayment.php";
$paypalAdapter = new PayPalAdapter($requester, $returnUrl, $cancelUrl, array(), );

?>