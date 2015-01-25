<?php

class Order {
    public function __construct($data) {
        $this->data = array();
        $this->parseAndAddData($data);
    }
    
    private function parseAndAddData($data) {
        foreach($data as $row) {
            if(isset($row["name"]) && isset($row["price"])) {
                $this->data[] = $row;
                continue;
            }
            $this->data[] = array("name" => $row[0], "price" => $row[1]);
        }
    }
    
    public function getLine($line) {
        return $this->data[$line];
    }
}

?>
