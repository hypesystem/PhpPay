<?php

namespace PhpPay {
    class PaymentHandler {
        public function __construct(PaymentAdapter $adapter) {
            $this->adapter = $adapter;
        }
        
        public function preparePayment(Order $order) {
            return $this->adapter->preparePayment($order);
        }
    }
}

?>
