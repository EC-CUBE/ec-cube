<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Kiyaku/SC_Helper_Kiyaku_TestBase.php");
/**
 *
 */
class SC_Helper_Kiyaku_rankDownTest extends SC_Helper_Kiyaku_TestBase
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

    public function testrankDownTest_ランクダウンができた場合_ランクを返す()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpKiyaku();
        $kiyaku_id = 1000;

        //期待値
        $this->expected = '11';

        $this->objKiyaku->rankDown($kiyaku_id);

        $col = 'rank';
        $from = 'dtb_kiyaku';
        $where = 'kiyaku_id = ?';
        $whereVal = array($kiyaku_id);
        $res = $objQuery->getCol($col, $from, $where, $whereVal);
        $this->actual = $res[0];
        $this->verify('ランクダウン');
    }
}
