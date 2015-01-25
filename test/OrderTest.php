<?php

include "vendor/autoload.php";
function phpPayAutoloader($class) {
    $file = "src/$class.php";
    if(file_exists($file)) {
        include $file;
    }
}
spl_autoload_register("phpPayAutoloader");

class OrderTest extends PHPUnit_Framework_TestCase {
    public function testCreatingAndGettingOrderLines() {
        $order = new Order(array(
            array("name" => "hello", "price" => 123.2),
            array("second", 112)
        ));
        
        $this->assertEquals($order->getLine(0)["name"], "hello");
        $this->assertEquals($order->getLine(0)["price"], 123.2);
        $this->assertEquals($order->getLine(1)["name"], "second");
        $this->assertEquals($order->getLine(1)["price"], 112);
    }
}

?>
