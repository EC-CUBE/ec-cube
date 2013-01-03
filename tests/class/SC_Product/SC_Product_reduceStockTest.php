<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/SC_Product/SC_Product_TestBase.php");
/**
 *
 */
class SC_Product_reduceStockTest extends SC_Product_TestBase {

    protected function setUp() {
        parent::setUp();
        $this->objProducts = new SC_Product_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testReduceStock_減少数０はFalse() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();
        
        $productClassId = '1001';
        $quantity = '0';
        $this->expected = false;
        $this->actual = $this->objProducts->reduceStock($productClassId, $quantity);

        $this->verify('減少数０');
    }
    
    public function testReduceStock_減少数1はTrue() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();
        
        $productClassId = '1001';
        $quantity = '1';
        $this->expected = true;
        $this->actual = $this->objProducts->reduceStock($productClassId, $quantity);

        $this->verify('減少数1');
    }
    
    public function testReduceStock_在庫数をマイナスにする数はFalse() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();
        
        $productClassId = '1001';
        $quantity = '100';
        $this->expected = false;
        $this->actual = $this->objProducts->reduceStock($productClassId, $quantity);

        $this->verify('在庫数マイナス');
    }
    
        
    public function testReduceStock_在庫数無限の場合はTrue() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();
        
        $productClassId = '1002';
        $quantity = '100';
        $this->expected = true;
        $this->actual = $this->objProducts->reduceStock($productClassId, $quantity);

        $this->verify('在庫数無限');
    }
}
