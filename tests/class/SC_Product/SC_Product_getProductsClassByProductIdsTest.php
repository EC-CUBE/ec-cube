<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/SC_Product/SC_Product_TestBase.php");
/**
 *
 */
class SC_Product_getProductsClassByProductIdsTest extends SC_Product_TestBase {

    protected function setUp() {
        parent::setUp();
        $this->objProducts = new SC_Product_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testGetProductsClassByProductIds_商品IDなしは空配列を返す() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();

        $this->expected = array();
        
        $productIds = array();

        $this->actual = $this->objProducts->getProductsClassByProductIds($productIds);

        $this->verify('商品ID指定なし');
    }
    
    public function testGetProductsClassByProductIds_指定の商品IDの情報を返す() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();

        $this->expected = array(
            0=> array(
                'product_id' => '1001'
                ,'del_flg' => '0'
                ,'point_rate' => '0'
                ,'stock' => '99'
                ,'stock_unlimited' => '0'
                ,'sale_limit' => null
                ,'price01' => '1500'
                ,'price02' => '1500'
                ,'product_code' => 'code1001'
                ,'product_class_id' => '1001'
                ,'product_type_id' => '1'
                ,'down_filename' => null
                ,'down_realfilename' => null
                ,'classcategory_name1' => 'cat1001'
                ,'rank1' => null
                ,'class_name1' => '味'
                ,'class_id1' => '1'
                ,'classcategory_id1' => '1001'
                ,'classcategory_id2' => '1002'
                ,'classcategory_name2' => 'cat1002'
                ,'rank2' => null
                ,'class_name2' => '味'
                ,'class_id2' => '1'
            )
        );
        
        $productIds = array('1001','2001');

        $this->actual = $this->objProducts->getProductsClassByProductIds($productIds);

        $this->verify('商品ID指定');
    }
    
    public function testGetProductsClassByProductIds_削除商品含む商品情報を返す() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();

        $this->expected = array(
            0=> array(
                'product_id' => '1001'
                ,'del_flg' => '0'
                ,'point_rate' => '0'
                ,'stock' => '99'
                ,'stock_unlimited' => '0'
                ,'sale_limit' => null
                ,'price01' => '1500'
                ,'price02' => '1500'
                ,'product_code' => 'code1001'
                ,'product_class_id' => '1001'
                ,'product_type_id' => '1'
                ,'down_filename' => null
                ,'down_realfilename' => null
                ,'classcategory_name1' => 'cat1001'
                ,'rank1' => null
                ,'class_name1' => '味'
                ,'class_id1' => '1'
                ,'classcategory_id1' => '1001'
                ,'classcategory_id2' => '1002'
                ,'classcategory_name2' => 'cat1002'
                ,'rank2' => null
                ,'class_name2' => '味'
                ,'class_id2' => '1'
            ),
            1=> array(
                'product_id' => '2001'
                ,'del_flg' => '1'
                ,'point_rate' => '0'
                ,'stock' => null
                ,'stock_unlimited' => '1'
                ,'sale_limit' => null
                ,'price01' => null
                ,'price02' => '2000'
                ,'product_code' => 'code2001'
                ,'product_class_id' => '2001'
                ,'product_type_id' => '1'
                ,'down_filename' => null
                ,'down_realfilename' => null
                ,'classcategory_name1' => null
                ,'rank1' => null
                ,'class_name1' => null
                ,'class_id1' => null
                ,'classcategory_id1' => '0'
                ,'classcategory_id2' => '0'
                ,'classcategory_name2' => null
                ,'rank2' => null
                ,'class_name2' => null
                ,'class_id2' => null
            )
        );
        
        $productIds = array('1001','2001');

        $this->actual = $this->objProducts->getProductsClassByProductIds($productIds,true);

        $this->verify('商品ID指定');
    }
    
    
}
