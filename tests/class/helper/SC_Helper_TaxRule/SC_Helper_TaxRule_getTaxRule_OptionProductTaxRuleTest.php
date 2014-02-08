<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
// 商品別税率機能無効
define('OPTION_PRODUCT_TAX_RULE', 1);
require_once($HOME . "/tests/class/helper/SC_Helper_TaxRule/SC_Helper_TaxRule_TestBase.php");

class SC_Helper_TaxRule_getTaxRule_OptionProductTaxRuleTest extends SC_Helper_TaxRule_TestBase
{

    protected function setUp()
    {
        parent::setUp();
        $this->objTaxRule = new SC_Helper_TaxRule_Ex();
        $this->setUpTax();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function 定数が正しく設定されているかのテスト()
    {
        $this->expected = 1;
        $this->actual = constant('OPTION_PRODUCT_TAX_RULE');
        $this->verify();
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function 引数がからの場合税率設定で設定かつ適用日時内の最新の値が返される()
    {
        $this->expected = array(
            'apply_date' => '2014-01-01 00:00:00',
            'tax_rate' => '5',
            'product_id' => '0',
            'product_class_id' => '0',
            'del_flg' => '0'
        );

        $return = $this->objTaxRule->getTaxRule();
        $this->actual = array(
            'apply_date' => $return['apply_date'],
            'tax_rate' => $return['tax_rate'],
            'product_id' => $return['product_id'],
            'product_class_id' => $return['product_class_id'],
            'del_flg' => $return['del_flg']
        );

        $this->verify();
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function 商品idを指定した場合商品に設定かつ適用日時内の最新の値が返される()
    {
        $this->expected = array(
            'apply_date' => '2014-02-02 00:00:00',
            'tax_rate' => '8',
            'product_id' => '1000',
            'product_class_id' => '0',
            'del_flg' => '0'
        );

        $return = $this->objTaxRule->getTaxRule(1000);
        $this->actual = array(
            'apply_date' => $return['apply_date'],
            'tax_rate' => $return['tax_rate'],
            'product_id' => $return['product_id'],
            'product_class_id' => $return['product_class_id'],
            'del_flg' => $return['del_flg']
        );

        $this->verify();
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function 商品規格idを指定した場合商品に登録かつ適用日時内の最新の値が返される()
    {
        $this->expected = array(
            'apply_date' => '2014-02-03 00:00:00',
            'tax_rate' => '9',
            'product_id' => '1000',
            'product_class_id' => '2000',
            'del_flg' => '0'
        );

        $return = $this->objTaxRule->getTaxRule(1000, 2000);
        $this->actual = array(
            'apply_date' => $return['apply_date'],
            'tax_rate' => $return['tax_rate'],
            'product_id' => $return['product_id'],
            'product_class_id' => $return['product_class_id'],
            'del_flg' => $return['del_flg']
        );

        $this->verify();
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function 商品規格idのみを指定した場合税率設定に登録かつ適用日時内の最新の値が返される()
    {
        $this->expected = array(
            'apply_date' => '2014-01-01 00:00:00',
            'tax_rate' => '5',
            'product_id' => '0',
            'product_class_id' => '0',
            'del_flg' => '0'
        );

        $return = $this->objTaxRule->getTaxRule(0, 2000);
        $this->actual = array(
            'apply_date' => $return['apply_date'],
            'tax_rate' => $return['tax_rate'],
            'product_id' => $return['product_id'],
            'product_class_id' => $return['product_class_id'],
            'del_flg' => $return['del_flg']
        );

        $this->verify();
    }
}
