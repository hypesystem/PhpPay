<?php

namespace PhpPay\PayPal {
    
    use PhpPay\PaymentAdapter;
    use PhpPay\Order;
    
    class PayPalAdapter extends PaymentAdapter {
        //TODO: The last arguments here should be named argumeents
        public function __construct($requester, $returnUrl, $cancelUrl, $options = array(), $useSandbox = false) {
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
            
            $this->returnUrl = $returnUrl;
            $this->cancelUrl = $cancelUrl;
    
            //Get options/reasonable defaults
            $this->options = array(
                "VERSION" => "109.0",
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
        
        private function requireUrl($url) {
            if(!preg_match('/^https?:\/\/.+\..+$/', $url)) {
                throw new InvalidArgumentException("Argument required to be an url, but was "+$url);
            }
        }
        
        private function requireOptions($options, $requiredOptions) {
            foreach($requiredOptions as $required) {
                $found = false;
                foreach($options as $actualKey => $val) {
                    if(strtoupper($actualKey) == $required) {
                        $found = true;
                        break;
                    }
                }
                if(!$found) {
                    throw new InvalidArgumentException("Missing required argument $required from options!");
                }
            }
        }
        
        public function preparePayment(Order $order) {
            $options = $this->getOptionsWithLineItems($order);
            $options["PAYMENTREQUEST_0_ITEMAMT"] = urlencode(number_format($order->getTotalPriceBeforeTax(), 2));
            $options["PAYMENTREQUEST_0_TAXAMT"] = urlencode(number_format($order->getTotalTax(), 2));
            $options["PAYMENTREQUEST_0_AMT"] = urlencode(number_format($order->getTotalPrice(), 2));
            
            $options["NOSHIPPING"] = 1;
            if($order->hasShipping()) {
                $options["NOSHIPPING"] = 0;
                $options["PAYMENTREQUEST_0_SHIPPINGAMT"] = urlencode(number_format($order->getShippingPrice(), 2));
            }
            
            $options["RETURNURL"] = $this->returnUrl;
            $options["CANCELURL"] = $this->cancelUrl;
            $options["METHOD"] = "SetExpressCheckout";
            
            $data = $this->convertOptionsToKeyValueData($options);
            $response = $this->requester->post($this->apiUrl, $data);
            $responseData = $this->parseData($response);
            
            if($responseData["ACK"] == "Failure") {
                trigger_error("Original data was ".$data);
                throw new Exception("Something went wrong while setting up express checkout! ".json_encode($responseData));
            }
            
            $token = $responseData["TOKEN"];
            
            $payment = new PayPalPreparedPayment($this->requester, $this->expressCheckoutUrl, $this->apiUrl, $token, $options["VERSION"], $options["USER"], $options["PWD"], $options["SIGNATURE"]);
            return $payment;
        }
        
        private function getOptionsWithLineItems(Order $order) {
            $options = $this->options;
            $total = 0;
            $lineItemCount = $order->getLineCount();
            for($i = 0; $i < $lineItemCount; $i++) {
                $item = $order->getLine($i);
                $options["L_PAYMENTREQUEST_0_NAME".$i] = urlencode($item["name"]);
                //TODO: Maybe L_PAYMENTREQUEST_0_{NUMBER,DESC}.$i (an item number) is required?
                $options["L_PAYMENTREQUEST_0_AMT".$i] = urlencode(number_format($item["priceBeforeTax"],2));
                //TODO: Support quantities
                $options["L_PAYMENTREQUEST_0_QTY".$i] = urlencode(1);
            }
            return $options;
        }
        
        private function convertOptionsToKeyValueData($options) {
            $first = true;
            $data = "";
            foreach($options as $key => $value) {
                if(!$first) $data .= "&";
                else $first = false;
                
                $data .= $key."=".$value;
            }
            return $data;
        }
        
        private function parseData($kvData) {
            $pairs = explode("&", $kvData);
            $data = array();
            foreach($pairs as $pair) {
                if($pair == "") continue;
                $kv = explode("=", $pair);
                $data[$kv[0]] = urldecode($kv[1]);
            }
            return $data;
        }
    }
}

?>
