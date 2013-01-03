<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/SC_Product/SC_Product_TestBase.php");
/**
 *
 */
class SC_Product_getBuyLimitTest extends SC_Product_TestBase {

    protected function setUp() {
        parent::setUp();
        $this->objProducts = new SC_Product_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testGetBuyLimit_商品数無限の場合販売制限なし() {
        
        $product = array('stock_unlimited' => '1'
                        ,'sale_limit' => null
                        ,'stock' => null);
        $this->expected = null;
        $this->actual = $this->objProducts->getBuyLimit($product);

        $this->verify('販売制限なし');
    }
    
    public function testGetBuyLimit_商品販売数制限数を返す() {
        
        $product = array('stock_unlimited' => '1'
                        ,'sale_limit' => 3
                        ,'stock' => null);
        $this->expected = 3;
        $this->actual = $this->objProducts->getBuyLimit($product);

        $this->verify('販売数制限');
    }
    
    public function testGetBuyLimit_商品在庫数を制限として返す() {
        
        $product = array('stock_unlimited' => null
                        ,'sale_limit' => null
                        ,'stock' => 5);
        $this->expected = 5;
        $this->actual = $this->objProducts->getBuyLimit($product);

        $this->verify('在庫数制限');
    }

    public function testGetBuyLimit_販売制限数大なり在庫数なら在庫数を返す() {
        
        $product = array('stock_unlimited' => null
                        ,'sale_limit' => 5
                        ,'stock' => 2);
        $this->expected = 2;
        $this->actual = $this->objProducts->getBuyLimit($product);

        $this->verify('販売数＞在庫数制限');
    }
    
    public function testGetBuyLimit_販売制限数少なり在庫数なら販売制限数() {
        
        $product = array('stock_unlimited' => null
                        ,'sale_limit' => 5
                        ,'stock' => 99);
        $this->expected = 5;
        $this->actual = $this->objProducts->getBuyLimit($product);

        $this->verify('販売数＜在庫数制限');
    }


    
}
