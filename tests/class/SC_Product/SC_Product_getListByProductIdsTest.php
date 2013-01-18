<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/SC_Product/SC_Product_TestBase.php");
/**
 *
 */
class SC_Product_getListsByProductIdsTest extends SC_Product_TestBase {

    protected function setUp() {
        parent::setUp();
        $this->objProducts = new SC_Product_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testGetListByProductIds_商品ID指定がない場合は空配列() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();

        $this->expected = array();

        $this->actual = $this->objProducts->getListByProductIds($this->objQuery);

        $this->verify('商品ID指定なし');
    }
    
    public function testGetListByProductIds_指定の商品IDで情報を取得する() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();
        
        $arrProductId = array('1001');
        //更新日を取得
        $arrRet = $this->objQuery->getCol('update_date','dtb_products', 'product_id = 1001');

        $this->expected = array(
            '1001' => array(
                'product_id' => '1001'
                ,'product_code_min' => 'code1001'
                ,'product_code_max' => 'code1001'
                ,'name' => '製品名1001'
                ,'comment1' => 'コメント10011'
                ,'comment2' => 'コメント10012'
                ,'comment3' => 'コメント10013'
                ,'main_list_comment' => 'リストコメント1001'
                ,'main_image' => '1001.jpg'
                ,'main_list_image' => '1001-main.jpg'
                ,'price01_min' => '1500'
                ,'price01_max' => '1500'
                ,'price02_min' => '1500'
                ,'price02_max' => '1500'
                ,'stock_min' => '99'
                ,'stock_max' => '99'
                ,'stock_unlimited_min' => '0'
                ,'stock_unlimited_max' => '0'
                ,'deliv_date_id' => '1'
                ,'status' => '1'
                ,'del_flg' => '0'
                ,'update_date' => $arrRet[0]
                ,'price01_min_inctax' => SC_Helper_DB::sfCalcIncTax('1500')
                ,'price01_max_inctax' => SC_Helper_DB::sfCalcIncTax('1500')
                ,'price02_min_inctax' => SC_Helper_DB::sfCalcIncTax('1500')
                ,'price02_max_inctax' => SC_Helper_DB::sfCalcIncTax('1500')
            )
        );

        $this->actual = $this->objProducts->getListByProductIds($this->objQuery, $arrProductId);

        $this->verify('商品ID指定');
    }
}
