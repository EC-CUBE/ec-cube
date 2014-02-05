<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_TaxRule/SC_Helper_TaxRule_TestBase.php");

class SC_Helper_TaxRule_setTaxRuleTest extends SC_Helper_TaxRule_TestBase
{

    protected function setUp()
    {
        parent::setUp();
        $this->objTaxRule = new SC_Helper_TaxRule_Ex();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////

    /**
     * @test
     */
    public function 新規登録が出来る()
    {
        // postgresとmysqlでmember_idのカラムに差がある
        $_SESSION['member_id'] = 1;
        $this->expected = array(
            'apply_date' => '2000-10-10 10:10:10',
            'calc_rule' => '1',
            'tax_rate' => '5',
        );
        $this->objTaxRule->setTaxRule(
            $this->expected['calc_rule'],
            $this->expected['tax_rate'],
            $this->expected['apply_date']);

        $result = $this->objQuery->select(
            'apply_date, calc_rule, tax_rate',
            'dtb_tax_rule',
            'apply_date = ?',
            array($this->expected['apply_date'])
        );

        $this->actual = $result[0];
        $this->verify();
    }
}
