<?php

abstract class PreparedPayment {
    abstract protected static function fromSerializedData($serializedData);
    abstract protected function getCheckoutLink();
    abstract protected function getSerializedData();
}

?>
