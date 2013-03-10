<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../../";
require_once($HOME . "/tests/class/Common_TestCase.php");
/**
 *
 */
class SC_Helper_Kiyaku_TestBase extends Common_TestCase
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
    protected function setUpKiyaku()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        
        $kiyaku = array(
            array(
                'kiyaku_id' => '1000',
                'kiyaku_title' => 'test1',
                'kiyaku_text' => 'test_text',
                'rank' => '12',
                'creator_id' => '0',
                'create_date' => '2000-01-01 00:00:00',
                'update_date' => '2000-01-01 00:00:00',
                'del_flg' => '0'
                  ),
            array(
                'kiyaku_id' => '1001',
                'kiyaku_title' => 'test2',
                'kiyaku_text' => 'test_text2',
                'rank' => '11',
                'creator_id' => '0',
                'create_date' => '2000-01-01 00:00:00',
                'update_date' => '2000-01-01 00:00:00',
                'del_flg' => '0'
                  ),
            array(
                'kiyaku_id' => '1002',
                'kiyaku_title' => 'test3',
                'kiyaku_text' => 'test_text',
                'rank' => '10',
                'creator_id' => '0',
                'create_date' => '2000-01-01 00:00:00',
                'update_date' => '2000-01-01 00:00:00',
                'del_flg' => '1'
                  )
                                );

        $this->objQuery->delete('dtb_kiyaku');
        foreach ($kiyaku as $key => $item) {
            $ret = $objQuery->insert('dtb_kiyaku', $item);
        }
    }
}