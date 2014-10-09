<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../../";
require_once($HOME . "/tests/class/Common_TestCase.php");
/**
 *
 */
class SC_Helper_Address_TestBase extends Common_TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * DBに規約情報を登録します.
     */
    protected function setUpAddress()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        // シーケンス初期化
        
        $kiyaku = array(
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
                'fax03' => '1111'
                  ),
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
                'fax03' => '1111'
                  )
                                );

        $this->objQuery->delete('dtb_other_deliv');
        foreach ($kiyaku as $key => $item) {
            $ret = $objQuery->insert('dtb_other_deliv', $item);
        }
    }
}