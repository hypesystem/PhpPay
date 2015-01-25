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
            if(isset($line["quantity"])) {
                $this->addLine($line["name"], $line["price"], $line["quantity"]);
                return;
            }
            $this->addLine($line["name"], $line["price"]);
            return;
        }
        if(!isset($line[0]) || !isset($line[1])) {
            throw new InvalidArgumentException("Invalid data given to Order: must contain (`name` AND `price`) OR (entries `0` AND `1`).");
        }
        if(isset($line[2])) {
            $this->addLine($line[0], $line[1], $line[2]);
            return;
        }
        $this->addLine($line[0], $line[1]);
    }
    
    public function addLine($name, $price, $quantity = 1) {
        $priceBeforeTax = round($price / (1 + $this->taxPct / 100),2);
        $tax = $price - $priceBeforeTax;
        $this->data[] = array(
                "name" => $name,
                "price" => $price,
                "priceBeforeTax" => $priceBeforeTax,
                "tax" => $tax,
                "quantity" => $quantity
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
    
    public function getTotalPrice() {
        $total = 0;
        foreach($this->data as $line) {
            $total += $line["price"];
        }
        return $total;
    }
    
    public function getTotalPriceBeforeTax() {
        $total = 0;
        foreach($this->data as $line) {
            $total += $line["priceBeforeTax"];
        }
        return $total;
    }
    
    public function getTotalTax() {
        $total = 0;
        foreach($this->data as $line) {
            $total += $line["tax"];
        }
        return $total;
    }
}

?>
