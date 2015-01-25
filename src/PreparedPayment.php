<?php

abstract class PreparedPayment {
    abstract protected function fromSerialized($serializedData);
    abstract protected function getCheckoutLink();
    abstract protected function getSerializedData();
}

?>
