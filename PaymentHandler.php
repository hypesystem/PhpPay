<?php

class PaymentHandler {
    public function __construct(PaymentAdapter $adapter) {
        $this->adapter = $adapter;
    }
    
    public function preparePayment(Order $order) {
        //should return a PreparedPayment
    }
}

?>
