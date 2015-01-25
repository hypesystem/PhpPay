<?php

abstract class PreparedPayment {
    abstract protected static function fromSerializedData($requester, $serializedData);
    abstract protected function getCheckoutLink();
    abstract protected function getSerializedData();
}

?>
