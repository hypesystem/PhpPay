<?php

class Order {
    public function __construct($taxPct, $data = array()) {
        $this->taxPct = $taxPct;
        $this->data = array();
        $this->shipping = null;
        $this->parseAndAddData($data);
    }
    
    private function parseAndAddData($data) {
        foreach($data as $row) {
            $this->parseAndAddLine($row);
        }
    }
    
    private function parseAndAddLine($line) {
        if(isset($line["type"]) && $line["type"] == "shipping") {
            $this->shipping = array("altName" => $line["altName"], "price" => $line["price"]);
            return;
        }
        if(isset($line["name"]) && isset($line["price"])) {
            $this->addLine($line["name"], $line["price"]);
            return;
        }
        if(!isset($line[0]) || !isset($line[1])) {
            throw new InvalidArgumentException("Invalid data given to Order: must contain (`name` AND `price`) OR (entries `0` AND `1`).");
        }
        $this->addLine($line[0], $line[1]);
    }
    
    public function addLine($name, $price) {
        $priceBeforeTax = round($price / (1 + $this->taxPct / 100),2);
        $tax = $price - $priceBeforeTax;
        $this->data[] = array(
                "name" => $name,
                "price" => $price,
                "priceBeforeTax" => $priceBeforeTax,
                "tax" => $tax
        );
    }
    
    public function getTaxPercentage() {
        return $this->taxPct;
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
    
    public function addShipping($price, $altName = "Shipping") {
        $this->shipping = array("altName" => $altName, "price" => $price);
    }
}

?>
