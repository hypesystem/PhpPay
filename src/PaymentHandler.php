<?php

class PaymentHandler {
    public function __construct(PaymentAdapter $adapter) {
        $this->adapter = $adapter;
    }
    
    public function preparePayment($id, Order $order) {
        $this->adapter->preparePayment($id, $order);
    }
}

?>
