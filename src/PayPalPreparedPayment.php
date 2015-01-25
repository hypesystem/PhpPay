<?php

class PayPalPreparedPayment extends PreparedPayment {
    public static function fromSerializedData($serializedData) {
        $dataAsArray = json_decode($serializedData);
        if($dataAsArray["type"] != get_class($this)) {
            throw new InvalidArgumentException("Type of serialized data {$dataAsArray["type"]} does not match this prepared payment class {get_class($this)}.");
        }
    }
    
    public function __construct($checkoutUrl, $token) {
        $this->token = "undefined";
    }
    
    public function getCheckoutLink() {
        return $this->checkoutUrl."?cmd=_express-checkout&token=".$this->token;
    }
    
    public function getSerializedData() {
        return json_encode(array(
            "type" => get_class($this),
            "token" => $this->token
        ));
    }
    
    public function execute() {
        
    }
}

?>
