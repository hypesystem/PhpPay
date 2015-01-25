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
    //NOTE: Prices are including tax. The tax is used for calculations when adding up the order.
    
    public function testCreatingAndGettingOrderLines() {
        $order = new Order(10, array(
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
        $order = new Order(10, array(
            array("name" => "hello", 12)
        ));
    }
    
    public function testCreatingEmptyOrderAndAddingDataLater() {
        $order = new Order(10);
        
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
        $order = new Order(10);
        
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
        $order = new Order(10, array(
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
        $order = new Order(10, array(
            array("hello", 123.2)
        ));

        $this->assertEquals($order->hasShipping(), false);

        $order->addShipping(12, "Transport cost");

        $this->assertEquals($order->hasShipping(), true);
        $this->assertEquals($order->getShippingPrice(), 12);
        $this->assertEquals($order->getShippingAltName(), "Transport cost");
    }

    public function testAddingShippingWithNoAlternativeName() {
        $order = new Order(10, array(
            array("hello", 123.2)
        ));

        $this->assertEquals($order->hasShipping(), false);

        $order->addShipping(12);

        $this->assertEquals($order->hasShipping(), true);
        $this->assertEquals($order->getShippingPrice(), 12);
        $this->assertEquals($order->getShippingAltName(), "Shipping");
    }
    
    public function testGettingTaxedAndUntaxedPrices() {
        $order = new Order(10, array(
            array("name" => "hello", "price" => 100),
            array("second", 60)
        ));
        
        $this->assertEquals($order->getLine(0)["priceBeforeTax"], 90.91);
        $this->assertEquals($order->getLine(0)["tax"], 9.09);
        $this->assertEquals($order->getLine(1)["priceBeforeTax"], 54.55);
        $this->assertEquals($order->getLine(1)["tax"], 5.45);
        
        $order = new Order(20, array(
            array("name" => "hello", "price" => 100),
            array("second", 60)
        ));
        
        $this->assertEquals($order->getLine(0)["priceBeforeTax"], 83.33);
        $this->assertEquals($order->getLine(0)["tax"], 16.67);
        $this->assertEquals($order->getLine(1)["priceBeforeTax"], 50);
        $this->assertEquals($order->getLine(1)["tax"], 10);
    }
    
    public function testGettingQuantityOfElementes() {
        $order = new Order(10, array(
            array("name" => "hello", "price" => 100),
            array("second", 60),
            array("quantized", 120, 3),
            array("name" => "labelled", "price" => 200, "quantity" => 4)
        ));
        
        $this->assertEquals($order->getLine(0)["quantity"], 1);
        $this->assertEquals($order->getLine(1)["quantity"], 1);
        $this->assertEquals($order->getLine(2)["quantity"], 3);
        $this->assertEquals($order->getLine(2)["price"], 120);
        $this->assertEquals($order->getLine(3)["quantity"], 4);
        $this->assertEquals($order->getLine(3)["price"], 200);
        
        $order->addLine("more", 130, 3);
        
        $this->assertEquals($order->getLine(4)["quantity"], 3);
        $this->assertEquals($order->getLine(4)["price"], 130);
    }
    
    public function testGettingTotalPrice() {
        $order = new Order(10, array(
            array("name" => "hello", "price" => 100),
            array("second", 60),
            array("quantized", 120, 3),
            array("name" => "labelled", "price" => 200, "quantity" => 4)
        ));
        
        $this->assertEquals($order->getTotalPrice(), 100 + 60 + 120 + 200);
    }
    
    public function testGettingTotalPriceBeforeTax() {
        $order = new Order(10, array(
            array("name" => "hello", "price" => 100),
            array("second", 60),
            array("quantized", 120, 3),
            array("name" => "labelled", "price" => 200, "quantity" => 4)
        ));
        
        $this->assertEquals($order->getTotalPriceBeforeTax(), 90.91 + 54.55 + 109.09 + 181.82);
    }
    
    public function testGettingTotalTax() {
        $order = new Order(10, array(
            array("name" => "hello", "price" => 100),
            array("second", 60),
            array("quantized", 120, 3),
            array("name" => "labelled", "price" => 200, "quantity" => 4)
        ));
        
        $this->assertEquals($order->getTotalTax(), 9.09 + 5.45 + 10.91 + 18.18);
    }
}

?>
