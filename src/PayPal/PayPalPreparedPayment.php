<?php

namespace PhpPay\PayPal {
    
    use PhpPay\PreparedPayment;
    
    class PayPalPreparedPayment extends PreparedPayment {
        public static function fromSerializedData($requester, $serializedData) {
            $dataAsArray = json_decode($serializedData);
            if($dataAsArray["type"] != get_class($this)) {
                throw new InvalidArgumentException("Type of serialized data {$dataAsArray["type"]} does not match this prepared payment class {get_class($this)}.");
            }
            return new PayPalPreparedPayment($requester, $dataAsArray["id"], $dataAsArray["checkoutUrl"], $dataAsArray["apiUrl"], $dataAsArray["token"], $dataAsArray["version"], $dataAsArray["user"], $dataAsArray["pwd"], $dataAsArray["signature"]);
        }
        
        public function __construct($requester, $id, $checkoutUrl, $apiUrl, $token, $version, $user, $pwd, $signature) {
            $this->id = $id;
            $this->token = $token;
            $this->checkoutUrl = $checkoutUrl;
            $this->apiUrl = $apiUrl;
            $this->version = $version;
            $this->user = $user;
            $this->pwd = $pwd;
            $this->signature = $signature;
        }
        
        public function getCheckoutUrl() {
            return $this->checkoutUrl."?cmd=_express-checkout&token=".$this->token;
        }
        
        public function getSerializedData() {
            return json_encode(array(
                "type" => get_class($this),
                "id" => $this->id,
                "token" => $this->token,
                "checkoutUrl" => $this->checkoutUrl,
                "apiUrl" => $this->apiUrl,
                "version" => $this->version,
                "user" => $this->user,
                "pwd" => $this->pwd,
                "signature" => $this->signature
            ));
        }
        
        public function execute($payerId) {
            //GetExpressCheckoutDetails
            
            //DoExpressCheckoutPayment
            
            //Return info
            
        }
    }
}

?>
