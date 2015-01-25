<?php

class Order {
    public function __construct($data = array()) {
        $this->data = array();
        $this->parseAndAddData($data);
    }
    
    private function parseAndAddData($data) {
        foreach($data as $row) {
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
}

?>
