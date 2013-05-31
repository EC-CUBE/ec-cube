<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Address/SC_Helper_Address_TestBase.php");
/**
 *
 */
class SC_Helper_Address_getListTest extends SC_Helper_Address_TestBase
{

    protected function setUp()
    {
        parent::setUp();
        $this->objAddress = new SC_Helper_Address_Ex();
    }

    protected function tearUp()
    {
        parent::tearUp();
    }

    /////////////////////////////////////////
    public function testgetListTest_会員が該当テーブルに存在するかつstart_idが指定されている場合_配送先のarrayを返す()
    {
        $this->setUpAddress();
        $customer_id = '1';
        $startno = '1';
        $this->expected = array(
            array(
                'other_deliv_id' => '1000',
                'customer_id' => '1',
                'name01' => 'テスト',
                'name02' => 'いち',
                'kana01' => 'テスト',
                'kana02' => 'イチ',
                'zip01' => '000',
                'zip02'=> '0000',
                'pref' => '1',
                'addr01' => 'テスト',
                'addr02' => 'テスト２',
                'tel01' => '000',
                'tel02' => '0000',
                'tel03' => '0000',
                'fax01' => '111',
                'fax02' => '1111',
                'fax03' => '1111',
                'country_id' => null
                  )

                                );
        $this->actual = $this->objAddress->getList($customer_id, $startno);
        
        $this->verify('配送先一覧取得');
    }

    public function testgetListTest_会員が該当テーブルに存在する場合_配送先のarrayを返す()
    {
        $this->setUpAddress();
        $customer_id = '1';
        $this->expected = array(
                        array(
                'other_deliv_id' => '1001',
                'customer_id' => '1',
                'name01' => 'テスト',
                'name02' => 'に',
                'kana01' => 'テスト',
                'kana02' => 'ニ',
                'zip01' => '222',
                'zip02'=> '2222',
                'pref' => '2',
                'addr01' => 'テスト1',
                'addr02' => 'テスト2',
                'tel01' => '000',
                'tel02' => '0000',
                'tel03' => '0000',
                'fax01' => '111',
                'fax02' => '1111',
                'fax03' => '1111',
                'country_id' => null
                  ),
            array(
                'other_deliv_id' => '1000',
                'customer_id' => '1',
                'name01' => 'テスト',
                'name02' => 'いち',
                'kana01' => 'テスト',
                'kana02' => 'イチ',
                'zip01' => '000',
                'zip02'=> '0000',
                'pref' => '1',
                'addr01' => 'テスト',
                'addr02' => 'テスト２',
                'tel01' => '000',
                'tel02' => '0000',
                'tel03' => '0000',
                'fax01' => '111',
                'fax02' => '1111',
                'fax03' => '1111',
                'country_id' => null
                  )

                                );
        $this->actual = $this->objAddress->getList($customer_id);
        
        $this->verify('配送先一覧取得');
    }


}
