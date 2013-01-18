<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/SC_Product/SC_Product_TestBase.php");
/**
 *
 */
class SC_Product_findProductCountTest extends SC_Product_TestBase {

    protected function setUp() {
        parent::setUp();
        $this->objProducts = new SC_Product_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testFindProductCount_すべての商品数を返す() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();

        $this->expected = 3;

        $this->actual = $this->objProducts->findProductCount($this->objQuery);

        $this->verify('商品数');
    }
    
    public function testFindProductCount_検索条件に一致する商品数を返す() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();
        
        $this->objQuery->setWhere('product_id = ?');
        $arrVal = array(1001);

        $this->expected = 1;

        $this->actual = $this->objProducts->findProductCount($this->objQuery, $arrVal);

        $this->verify('検索商品数');
    }
    
}
