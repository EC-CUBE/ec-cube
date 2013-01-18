<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/SC_Product/SC_Product_TestBase.php");
/**
 *
 */
class SC_Product_findProductsOrderTest extends SC_Product_TestBase {

    protected function setUp() {
        parent::setUp();
        $this->objProducts = new SC_Product_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testFindProductIdsOrder_商品ID降順() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();

        // 商品ID降順で商品IDを取得する
        $this->objQuery->setOrder('product_id DESC');
        $this->expected = array('2001','1002', '1001');

        $this->actual = $this->objProducts->findProductIdsOrder($this->objQuery);

        $this->verify('商品ID降順');
    }

    public function testFindProductIdsOrder_商品名昇順() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();

        // 商品名昇順で商品IDを取得する
        $this->objQuery->setOrder('product_id ASC');
        $this->expected = array('1001', '1002','2001');

        $this->actual = $this->objProducts->findProductIdsOrder($this->objQuery);

        $this->verify('商品ID昇順');
    }
    
    public function testFindProductIdsOrder_arrOrderDataの設定による並び順() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();

        // setProductsOrderを行う
        $this->objProducts->setProductsOrder('product_id');
        $this->expected = array('1001', '1002','2001');

        $this->actual = $this->objProducts->findProductIdsOrder($this->objQuery);

        $this->verify('arrOrderData設定順');
    }
    
}
