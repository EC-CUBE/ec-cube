<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/SC_Product/SC_Product_TestBase.php");
/**
 *
 */
class SC_Product_setProductsOrderTest extends SC_Product_TestBase {

    protected function setUp() {
        parent::setUp();
        $this->objProducts = new SC_Product_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////
    public function testSetProductsOrder_デフォルト引数() {
        $this->objProducts->setProductsOrder('name');

        $this->actual = $this->objProducts->arrOrderData;
        $this->expected = array('col' => 'name', 'table' => 'dtb_products', 'order' => 'ASC');

        $this->verify('デフォルト引数');
    }

    public function testSetProductsOrder_引数指定() {
        $this->objProducts->setProductsOrder('name', 'dtb_products_class', 'DESC');

        $this->actual = $this->objProducts->arrOrderData;
        $this->expected = array('col' => 'name', 'table' => 'dtb_products_class', 'order' => 'DESC');

        $this->verify('デフォルト引数');
    }
}
