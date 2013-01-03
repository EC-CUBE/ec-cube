<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/SC_Product/SC_Product_TestBase.php");
/**
 *
 */
class SC_Product_getProductStatusTest extends SC_Product_TestBase {

    protected function setUp() {
        parent::setUp();
        $this->objProducts = new SC_Product_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testGetProductStatus_商品IDなしは空の配列を返す() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();
        $this->setUpProductStatus();

        $this->expected = array();
        $productIds = null;

        $this->actual = $this->objProducts->getProductStatus($productIds);

        $this->verify('空の配列');
    }
    
    public function testGetProductStatus_指定した商品IDの商品ステータスを返す() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();
        $this->setUpProductStatus();

        $this->expected = array('1001' => array('1'));
        $productIds = array('1001');

        $this->actual = $this->objProducts->getProductStatus($productIds);

        $this->verify('商品ステータス');
    }
    
    
}
