<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Kiyaku/SC_Helper_Kiyaku_TestBase.php");
/**
 *
 */
class SC_Helper_Kiyaku_getKiyakuTest extends SC_Helper_Kiyaku_TestBase
{

    protected function setUp()
    {
        parent::setUp();
        $this->objKiyaku = new SC_Helper_Kiyaku_Ex();
    }

    protected function tearUp()
    {
        parent::tearUp();
    }

    /////////////////////////////////////////

    public function testgetKiyakuTest_規約情報を取得できた場合_規約のarrayを返す()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpKiyaku();
        $has_deleted = FALSE;
        $kiyaku_id = 1000;
        //期待値
        $this->expected = array(
            'kiyaku_id' => '1000',
            'kiyaku_title' => 'test1',
            'kiyaku_text' => 'test_text',
            'rank' => '12',
            'creator_id' => '0',
            'create_date' => '2000-01-01 00:00:00',
            'update_date' => '2000-01-01 00:00:00',
            'del_flg' => '0'
                                );

        $this->actual = $this->objKiyaku->getKiyaku($kiyaku_id, $has_deleted);
        $this->verify('規約詳細取得');
    }

    public function testgetKiyakuTest_規約情報を規約idから取得する際削除された規約を指定した場合_nullを返す()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpKiyaku();
        $has_deleted = FALSE;
        $kiyaku_id = 1002;
        //期待値
        $this->expected = null;

        $this->actual = $this->objKiyaku->getKiyaku($kiyaku_id, $has_deleted);
        $this->verify('規約詳細取得');
    }

    public function testgetKiyakuTest_削除された情報を含む規約情報を規約idから取得する際削除された規約を指定した場合_nullを返す()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpKiyaku();
        $has_deleted = TRUE;
        $kiyaku_id = 1002;
        //期待値
        $this->expected = array(
                'kiyaku_id' => '1002',
                'kiyaku_title' => 'test3',
                'kiyaku_text' => 'test_text',
                'rank' => '10',
                'creator_id' => '0',
                'create_date' => '2000-01-01 00:00:00',
                'update_date' => '2000-01-01 00:00:00',
                'del_flg' => '1'
                                );

        $this->actual = $this->objKiyaku->getKiyaku($kiyaku_id, $has_deleted);
        $this->verify('規約詳細取得');
    }

}
