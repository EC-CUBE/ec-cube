<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Kiyaku/SC_Helper_Kiyaku_TestBase.php");
/**
 *
 */
class SC_Helper_Kiyaku_getListTest extends SC_Helper_Kiyaku_TestBase
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

    public function testgetListTest_削除した商品も含んだ一覧を取得できた場合_一覧のarrayを返す()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpKiyaku();
        $has_deleted = TRUE;
        //期待値
        $this->expected = array(
            array(
                'kiyaku_id' => '1000',
                'kiyaku_title' => 'test1',
                'kiyaku_text' => 'test_text'
                  ),
            array(
                'kiyaku_id' => '1001',
                'kiyaku_title' => 'test2',
                'kiyaku_text' => 'test_text2'
                  ),
            array(
                'kiyaku_id' => '1002',
                'kiyaku_title' => 'test3',
                'kiyaku_text' => 'test_text'
                  )
                                );

        $this->actual = $this->objKiyaku->getList($has_deleted);
        $this->verify('規約一覧取得');
    }

    public function testgetListTest_一覧を取得できた場合削除した商品は取得しない_一覧のarrayを返す()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpKiyaku();
        $has_deleted = FALSE;
        //期待値
        $this->expected = array(
            array(
                'kiyaku_id' => '1000',
                'kiyaku_title' => 'test1',
                'kiyaku_text' => 'test_text'
                  ),
            array(
                'kiyaku_id' => '1001',
                'kiyaku_title' => 'test2',
                'kiyaku_text' => 'test_text2'
                  ),
                                );

        $this->actual = $this->objKiyaku->getList($has_deleted);
        $this->verify('規約一覧取得');
    }

}
