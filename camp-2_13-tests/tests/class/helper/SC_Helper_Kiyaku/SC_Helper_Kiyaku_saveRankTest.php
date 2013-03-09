<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Kiyaku/SC_Helper_Kiyaku_TestBase.php");
/**
 *
 */
class SC_Helper_Kiyaku_saveRankTest extends SC_Helper_Kiyaku_TestBase
{

    protected function setUp()
    {
        parent::setUp();
        $this->objKiyaku = new SC_Helper_Kiyaku_Ex();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testsaveRankTest_新規で規約を登録する場合_3を返す()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpKiyaku();
                $sqlval = array(
                'kiyaku_title' => '第3条 (TEST)',
                'kiyaku_text' => 'testKiyaku',
                'rank' => '',
                'creator_id' => '0',
                'create_date' => '2000-01-01 00:00:00',
                'update_date' => '2000-01-01 00:00:00',
                'del_flg' => '0'
                                );
        $this->expected = 4;
        // シーケンス調整
        $sqlval['kiyaku_id'] = $objQuery->setVal('dtb_kiyaku_kiyaku_id', 4);
        $this->actual = $this->objKiyaku->saveRank($sqlval);

        $this->verify('新規規約登録');
    }

    public function testsaveRankTest_規約を更新する場合_2を返す()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpKiyaku();
                $sqlval = array(
                'kiyaku_id' => '2',
                'kiyaku_title' => '第2条 (登録)',
                'kiyaku_text' => 'test kiyaku2',
                'rank' => '11',
                'creator_id' => '0',
                'create_date' => '2000-01-01 00:00:00',
                'update_date' => '2000-01-01 00:00:00',
                'del_flg' => '0'
                                );
        $this->expected = 2;
        // シーケンス調整
        $this->actual = $this->objKiyaku->saveRank($sqlval);

        $this->verify('新規規約登録');
    }
}
