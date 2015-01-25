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
        
        $this->assertEquals($order->getLineCount(), 2);
        $this->assertEquals($order->getLine(0)["name"], "hello");
        $this->assertEquals($order->getLine(0)["price"], 123.2);
        $this->assertEquals($order->getLine(1)["name"], "second");
        $this->assertEquals($order->getLine(1)["price"], 112);
        $this->assertEquals($order->hasShipping(), false);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreatingWithInvalidDataFails() {
        $order = new Order(array(
            array("name" => "hello", 12)
        ));
    }
    
    public function testCreatingEmptyOrderAndAddingDataLater() {
        $order = new Order();
        
        $this->assertEquals($order->getLineCount(), 0);
        
        $order->addLines(array(
            array("name" => "hello", "price" => 123.2),
            array("second", 112)
        ));
        
        $this->assertEquals($order->getLineCount(), 2);
        $this->assertEquals($order->getLine(0)["name"], "hello");
        $this->assertEquals($order->getLine(0)["price"], 123.2);
        $this->assertEquals($order->getLine(1)["name"], "second");
        $this->assertEquals($order->getLine(1)["price"], 112);
        $this->assertEquals($order->hasShipping(), false);
    }
    
    public function testAddingASingleLine() {
        $order = new Order();
        
        $this->assertEquals($order->getLineCount(), 0);
        
        $order->addLine("hello", 123.2);
        
        $this->assertEquals($order->getLineCount(), 1);
        $this->assertEquals($order->getLine(0)["name"], "hello");
        $this->assertEquals($order->getLine(0)["price"], 123.2);
        
        $order->addLine("second", 112);
        
        $this->assertEquals($order->getLineCount(), 2);
        $this->assertEquals($order->getLine(1)["name"], "second");
        $this->assertEquals($order->getLine(1)["price"], 112);
    }
    
    public function testCreatingWithShipping() {
        $order = new Order(array(
            array("name" => "hello", "price" => 123.2),
            array("second", 112),
            array("type" => "shipping", "altName" => "Transport cost", "price" => 12)
        ));
        
        $this->assertEquals($order->getLineCount(), 2);
        $this->assertEquals($order->getLine(0)["name"], "hello");
        $this->assertEquals($order->getLine(0)["price"], 123.2);
        $this->assertEquals($order->getLine(1)["name"], "second");
        $this->assertEquals($order->getLine(1)["price"], 112);
        $this->assertEquals($order->hasShipping(), true);
        $this->assertEquals($order->getShippingPrice(), 12);
        $this->assertEquals($order->getShippingAltName(), "Transport cost");
    }

    public function testAddingShipping() {
        $order = new Order(array(
            array("hello", 123.2)
        ));

        $this->assertEquals($order->hasShipping(), false);

        $order->addShipping(12, "Transport cost");

        $this->assertEquals($order->hasShipping(), true);
        $this->assertEquals($order->getShippingPrice(), 12);
        $this->assertEquals($order->getShippingAltName(), "Transport cost");
    }

    public function testAddingShippingWithNoAlternativeName() {
        $order = new Order(array(
            array("hello", 123.2)
        ));

        $this->assertEquals($order->hasShipping(), false);

        $order->addShipping(12);

        $this->assertEquals($order->hasShipping(), true);
        $this->assertEquals($order->getShippingPrice(), 12);
        $this->assertEquals($order->getShippingAltName(), "Shipping");
    }
}

?>
