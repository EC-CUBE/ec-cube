<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/SC_Product/SC_Product_TestBase.php");
/**
 *
 */
class SC_Product_getDetailTest extends SC_Product_TestBase
{

    protected function setUp()
    {
        parent::setUp();
        $this->objProducts = new SC_Product_Ex();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testGetDetail_商品IDの詳細情報を返す()
    {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();
        
        //更新日を取得
        $arrRet = $this->objQuery->getCol('update_date','dtb_products', 'product_id = 1001');

        $this->expected = array(
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
                ,'price01_min_inctax' => SC_Helper_DB_Ex::sfCalcIncTax('1500')
                ,'price01_max_inctax' => SC_Helper_DB_Ex::sfCalcIncTax('1500')
                ,'price02_min_inctax' => SC_Helper_DB_Ex::sfCalcIncTax('1500')
                ,'price02_max_inctax' => SC_Helper_DB_Ex::sfCalcIncTax('1500')
                ,'maker_id' => null
                ,'comment4' => null
                ,'comment5' => null
                ,'comment6' => null
                ,'note' => null
                ,'main_comment' => 'メインコメント1001'
                ,'main_large_image' => null
                ,'sub_title1' => null
                ,'sub_comment1' => null
                ,'sub_image1' => null
                ,'sub_large_image1' => null
                ,'sub_title2' => null
                ,'sub_comment2' => null
                ,'sub_image2' => null
                ,'sub_large_image2' => null
                ,'sub_title3' => null
                ,'sub_comment3' => null
                ,'sub_image3' => null
                ,'sub_large_image3' => null
                ,'sub_title4' => null
                ,'sub_comment4' => null
                ,'sub_image4' => null
                ,'sub_large_image4' => null
                ,'sub_title5' => null
                ,'sub_comment5' => null
                ,'sub_image5' => null
                ,'sub_large_image5' => null
                ,'sub_title6' => null
                ,'sub_comment6' => null
                ,'sub_image6' => null
                ,'sub_large_image6' => null
                ,'creator_id' => '1'
                ,'create_date' => $arrRet[0]
                ,'point_rate' => '0'
                ,'deliv_fee' => null
                ,'class_count' => '1'
                ,'maker_name' => null
        );

        $this->actual = $this->objProducts->getDetail('1001');

        $this->verify('商品詳細');
    }
    
}
