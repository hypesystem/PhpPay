<?php

class PayPalAdapter extends PaymentAdapter {
    //TODO: The last arguments here should be named argumeents
    public function __construct($requester, $returnUrl, $cancelUrl, $options = array(), $identifyingArgumentName = "id", $useSandbox = false) {
        $this->requester = $requester;
        $this->requireUrl($returnUrl);
        $this->requireUrl($cancelUrl);
        $this->requireOptions($options, array("USER", "PWD", "SIGNATURE"));
        
        //Handle sandbox/not sandbox
        $this->apiUrl = "https://api-3t.paypal.com/nvp";
        $this->expressCheckoutUrl = "https://www.paypal.com/cgi-bin/webscr";
        if($useSandbox) {
            $this->apiUrl = "https://api-3t.sandbox.paypal.com/nvp";
            $this->expressCheckoutUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        }
        $this->sandbox = $useSandbox;
        
        $this->identifyingArgumentName = $identifyingArgumentName;
        
        $this->returnUrl = $returnUrl;
        $this->cancelUrl = $cancelUrl;

        //Get options/reasonable defaults
        $this->options = array(
            "VERSION" => 109.0,
            "SOLUTIONTYPE" => "Sole",
            "LANDINGPAGE" => "Billing",
            "PAYMENTREQUEST_0_PAYMENTACTION" => "Sale",
            "LOCALECODE" => "en_US",
            "PAYMENTREQUEST_0_CURRENCYCODE" => "USD"
        );
        foreach($options as $key => $value) {
            $key = strtoupper($key);
    
            if($key == "VERSION" && $value != $this->options["VERSION"]) {
                trigger_error("The PayPalAdapter is configured with a custom version '$value', but the library is only guaranteed to work with '{$this->options["VERSION"]}'.", E_USER_WARNING);
            }
            if($key == "RETURNURL" || $key == "CANCELURL") {
                throw new InvalidArgumentException("Cannot set $key in PayPalAdapter. It must be set as an argument in the constructor.");
            }

            $this->options[$key] = $value;
        }
    }
    
    public function preparePayment($identifyingValue, Order $order) {
        $options = $this->getOptionsWithLineItems($order);
        $options["PAYMENTREQUEST_0_ITEMAMT"] = urlencode(number_format($order->getTotalPriceBeforeTax(), 2));
        $options["PAYMENTREQUEST_0_TAXAMT"] = urlencode(number_format($order->getTotalTax(), 2));
        $options["PAYMENTREQUEST_0_AMT"] = urlencode(number_format($order->getTotalPrice(), 2));
        
        $options["NOSHIPPING"] = 1;
        if($order->hasShipping()) {
            $options["NOSHIPPING"] = 0;
            $options["PAYMENTREQUEST_0_SHIPPINGAMT"] = urlencode(number_format($order->getShippingPrice(), 2));
        }
        
        $options["RETURNURL"] = $this->composeReturnUrl($identifyingValue);
        $options["CANCELURL"] = $this->composeCancelUrl($identifyingValue);
        $options["METHOD"] = "SetExpressCheckout";
        
        $data = implode("&", $options);
        $response = $this->requester->post($this->apiUrl, $data);
        $responseData = $this->parseData($response);
        $token = $responseData["TOKEN"];
        
        return new PayPalPreparedPayment($this->requester, $identifyingValue, $this->expressCheckoutUrl, $this->apiUrl, $token, $options["VERSION"], $options["USER"], $options["PWD"], $options["SIGNATURE"]);
    }
    
    private function getOptionsWithLineItems(Order $order) {
        $options = $this->options;
        $total = 0;
        $lineItemCount = $order->getLineCount();
        for($i = 0; $i < $lineItemCount; $i++) {
            $item = $order->getLine($i);
            $options["L_PAYMENTREQUEST_0_NAME".$i] = urlencode($item["name"]);
            //TODO: Maybe L_PAYMENTREQUEST_0_{NUMBER,DESC}.$i (an item number) is required?
            $options["L_PAYMENTREQUEST_0_AMT".$i] = urlencode($item["priceBeforeTax"]);
            $options["L_PAYMENTREQUEST_0_QTY".$i] = urlencode($item["quantity"]);
        }
        return $options;
    }
    
    private function composeReturnUrl($id) {
        return $this->composeUrl($this->returnUrl, array(
            $this->identifyingArgumentName => $id
        ));
    }
    
    private function composeUrl($url, $queries) {
        //Do something clever...
        return $url.$this->buildQueryString($queries);
    }
    
    private function buildQueryString($queryMap) {
        $queryStrings = array();
        foreach($queryMap as $key => $value) {
            $queryStrings[] = $key."=".$value;
        }
        return "?".implode("&",$queryStrings);
    }
    
    private function composeCancelUrl($id) {
        return $this->composeUrl($this->cancelUrl, array(
            $this->identifyingArgumentName => $id
        ));
    }
}

?>
