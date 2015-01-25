<?php

abstract class PreparedPayment {
    public static function fromSerializedData($requester, $serializedData) {
        throw new Exception("PreparedPayment#fromSerializedData must be overwritten!");
    }
    abstract protected function getCheckoutUrl();
    abstract protected function getSerializedData();
}

?>
