<?php

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/SC_CartSession/SC_CartSession_TestBase.php");

/**
 * SC_CartSession_getCartListTest
 *
 * @package
 * @version $id$
 * @copyright
 * @author Nobuhiko Kimoto <info@nob-log.info>
 * @license
 */
class SC_CartSession_getCartListTest extends SC_CartSession_TestBase
{

    protected function setUp()
    {
        parent::setUp();
        $this->objCartSession = new SC_CartSession_Ex();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////

    /**
     * @test
     */
    public function getCartList_商品を追加していなければ空の配列を返す()
    {
        $this->setUpProductClass();
        //$this->setUpProducts();
        //$this->setUpClassCategory();

        $this->expected = 0;

        $this->actual = count($this->objCartSession->getCartList(1));

        $this->verify('商品数');
    }

    /**
     * @test
     */
    public function getCartList_商品を1つ追加した場合1つの配列を返す()
    {
        $this->setUpProductClass();
        //$this->setUpProducts();
        //$this->setUpClassCategory();

        $this->expected = 1;
        $this->objCartSession->addProduct('1001', 1);

        $cartList = $this->objCartSession->getCartList(1);
        $this->actual = count($cartList);

        $this->verify('商品数');

        return $cartList;
    }

    /**
     * @test
     * @depends getCartList_商品を1つ追加した場合1つの配列を返す
     */
    public function getCartList_商品を1つ追加した場合合計数は1($cartList)
    {
        $this->expected = 1;
        $this->actual = $cartList[0]['quantity'];
        $this->verify('商品追加数');
    }

    /**
     * @test
     * @depends getCartList_商品を1つ追加した場合1つの配列を返す
     */
    public function getCartList_商品を1つ追加した場合商品データを返す($cartList)
    {
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
            ,'price01_min_inctax' => SC_Helper_TaxRule_Ex::sfCalcIncTax('1500')
            ,'price01_max_inctax' => SC_Helper_TaxRule_Ex::sfCalcIncTax('1500')
            ,'price02_min_inctax' => SC_Helper_TaxRule_Ex::sfCalcIncTax('1500')
            ,'price02_max_inctax' => SC_Helper_TaxRule_Ex::sfCalcIncTax('1500')
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
            ,'point_rate' => '0'
            ,'deliv_fee' => null
            ,'class_count' => '1'
            ,'maker_name' => null
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
            ,'price01_inctax' => 1575.0
            ,'price02_inctax' => 1575.0
            //,'create_date' => '2014-05-04 12:20:29'
            //,'update_date' => '2014-05-04 12:20:29'
        );

        // 時間はずれるので配列から削除
        unset($cartList[0]['productsClass']['update_date']);
        unset($cartList[0]['productsClass']['create_date']);

        $this->actual = $cartList[0]['productsClass'];
        $this->verify('商品詳細');
    }


    /**
     * @test
     */
    public function getCartList_削除済み商品を追加した場合はカートは空()
    {
        $this->setUpProductClass();
        //$this->setUpProducts();
        //$this->setUpClassCategory();

        $this->expected = 0;
        $this->objCartSession->addProduct('2001', 1);

        $cartList = $this->objCartSession->getCartList(1);
        $this->actual = count($cartList);
        $this->verify('商品数');
    }

    /**
     * @test
     */
    /* バグな気がする？
    public function getCartList_非表示商品を追加した場合はカートは空()
    {
        $this->setUpProductClass();
        $this->setUpProducts();
        $this->setUpClassCategory();

        $this->expected = 0;
        $this->objCartSession->addProduct('1002', 1);
        $cartList = $this->objCartSession->getCartList(2);
        $this->actual = count($cartList);
        $this->verify('商品数');
    }*/

    /**
     * @test
     */
    public function getCartList_複数商品種別の30商品を追加した場合カートに30商品追加されている()
    {
        $this->setUpBigProductClass();
        $this->expected = 30;
        for ($i = 3000; $i < 3030; $i++) {
            $this->objCartSession->addProduct($i, 1);
        }
        $cartList = count($this->objCartSession->getCartList(1)) + count($this->objCartSession->getCartList(2)) + count($this->objCartSession->getCartList(3));
        $this->actual = $cartList;

        $this->verify('商品数');
    }
}
