<?php

class PayPalAdapter extends PaymentAdapter {
    public function __construct($returnUrl, $cancelUrl, $options, $useSandbox = false) {
        //Handle sandbox/not sandbox
        $this->apiUrl = "https://api-3t.paypal.com/nvp";
        $this->expressCheckoutUrl = "https://www.paypal.com/cgi-bin/webscr";
        if($useSandbox) {
            $this->apiUrl = "https://api-3t.sandbox.paypal.com/nvp";
            $this->expressCheckoutUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        }
        $this->sandbox = $useSandbox;

        //Get options/reasonable defaults
        $this->options = array(
            "VERSION" => 109.0
        );
        foreach($options as $key => $value) {
            $key = strtoupper($key);
    
            if($key == "VERSION" && $value != $this->options["VERSION"]) {
                trigger_error("The PayPalAdapter is configured with a custom version '$value', but the library is only guaranteed to work with '{$this->options["VERSION"]}'.", E_USER_WARNING);
            }

            $this->options[$key] = $value;
        }
    }
}

?>
