<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/SC_Product/SC_Product_TestBase.php");
/**
 *
 */
class SC_Product_setProductStatusTest extends SC_Product_TestBase {

    protected function setUp() {
        parent::setUp();
        $this->objProducts = new SC_Product_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testSetProductStatus_登録した商品ステータスを返す() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();
        $this->setUpProductStatus();
        
        $this->objProducts->setProductStatus('1001', array('2','3','4'));

        $this->expected = array('1001'=>array('2','3','4'));
        $productIds = array('1001');
        $this->actual = $this->objProducts->getProductStatus($productIds);

        $this->verify('商品ステータスの更新');
    }
    
}
