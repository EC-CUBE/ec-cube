<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/SC_Product/SC_Product_TestBase.php");
/**
 *
 */
class SC_Product_listsTest extends SC_Product_TestBase {

    protected function setUp() {
        parent::setUp();
        $this->objProducts = new SC_Product_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testlists_商品一覧取得() {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();
        //更新日を取得
        $arrRet = $this->objQuery->getCol('update_date','dtb_products', 'product_id = 1001');

        $this->expected = array(
            0 => array(
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
            )
            ,1 => array(
                'product_id' => '1002'
                ,'product_code_min' => 'code1002'
                ,'product_code_max' => 'code1002'
                ,'name' => '製品名1002'
                ,'comment1' => 'コメント10021'
                ,'comment2' => 'コメント10022'
                ,'comment3' => 'コメント10023'
                ,'main_list_comment' => 'リストコメント1002'
                ,'main_image' => '1002.jpg'
                ,'main_list_image' => '1002-main.jpg'
                ,'price01_min' => null
                ,'price01_max' => null
                ,'price02_min' => '2500'
                ,'price02_max' => '2500'
                ,'stock_min' => null
                ,'stock_max' => null
                ,'stock_unlimited_min' => '1'
                ,'stock_unlimited_max' => '1'
                ,'deliv_date_id' => '2'
                ,'status' => '2'
                ,'del_flg' => '0'
                ,'update_date' => $arrRet[0]

            )
        );

        $this->actual = $this->objProducts->lists($this->objQuery);

        $this->verify('商品一覧');
    }
    
}
