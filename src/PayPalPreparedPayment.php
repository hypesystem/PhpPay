<?php

class PayPalPreparedPayment extends PreparedPayment {
    public static function fromSerializedData($serializedData) {
        $dataAsArray = json_decode($serializedData);
        if($dataAsArray["type"] != get_class($this)) {
            throw new InvalidArgumentException("Type of serialized data {$dataAsArray["type"]} does not match this prepared payment class {get_class($this)}.");
        }
    }
    
    public function __construct($id, $checkoutUrl, $token, $user, $pwd, $signature) {
        $this->id = $id;
        $this->token = $token;
        $this->checkoutUrl = $checkoutUrl;
        $this->user = $user;
        $this->pwd = $pwd;
        $this->signature = $signature;
    }
    
    public function getCheckoutLink() {
        return $this->checkoutUrl."?cmd=_express-checkout&token=".$this->token;
    }
    
    public function getSerializedData() {
        return json_encode(array(
            "type" => get_class($this),
            "id" => $this->id,
            "token" => $this->token,
            "checkoutUrl" => $this->checkoutUrl,
            "user" => $this->user,
            "pwd" => $this->pwd,
            "signature" => $this->signature
        ));
    }
    
    public function execute() {
        
    }
}

?>
