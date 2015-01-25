<?php

class Order {
    public function __construct($data = array()) {
        $this->data = array();
        $this->shipping = null;
        $this->parseAndAddData($data);
    }
    
    private function parseAndAddData($data) {
        foreach($data as $row) {
            if(isset($row["type"]) && $row["type"] == "shipping") {
                $this->shipping = array("altName" => $row["altName"], "price" => $row["price"]);
                continue;
            }
            if(isset($row["name"]) && isset($row["price"])) {
                $this->data[] = $row;
                continue;
            }
            if(!isset($row[0]) || !isset($row[1])) {
                throw new InvalidArgumentException("Invalid data given to Order: must contain (`name` AND `price`) OR (entries `0` AND `1`).");
            }
            $this->data[] = array("name" => $row[0], "price" => $row[1]);
        }
    }
    
    public function getLine($line) {
        return $this->data[$line];
    }
    
    public function getLineCount() {
        return sizeof($this->data);
    }
    
    public function addLines($data) {
        $this->parseAndAddData($data);
    }
    
    public function addLine($name, $price) {
        $this->parseAndAddData(array(array($name,$price)));
    }

    public function hasShipping() {
        return $this->shipping != null;
    }

    public function getShippingPrice() {
        if(!$this->hasShipping()) {
            throw new Exception("Cannot get Shipping price when order does not have shipping. Check Order#hasShipping() first.");
        }
        return $this->shipping["price"];
    }

    public function getShippingAltName() {
        if(!$this->hasShipping()) {
            throw new Exception("Cannot get Shipping alt name when order does not have shipping. Check Order#hasShipping() first.");
        }
        return $this->shipping["altName"];
    }
}

?>
