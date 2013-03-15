<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Kiyaku/SC_Helper_Kiyaku_TestBase.php");
/**
 *
 */
class SC_Helper_Kiyaku_deleteKiyakuTest extends SC_Helper_Kiyaku_TestBase
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

    public function testdeleteKiyakuTest_削除ができた場合_del_flgの1を返す()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpKiyaku();
        $kiyaku_id = 1001;

        //期待値
        $this->expected = "1";

        $this->objKiyaku->deleteKiyaku($kiyaku_id);

        $col = 'del_flg';
        $from = 'dtb_kiyaku';
        $where = 'kiyaku_id = ?';
        $whereVal = array($kiyaku_id);
        $res = $objQuery->getCol($col, $from, $where, $whereVal);
        $this->actual = $res[0];
        $this->verify('ランク削除');
    }
}
