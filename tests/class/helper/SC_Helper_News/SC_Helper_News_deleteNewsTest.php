<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_News/SC_Helper_News_TestBase.php");
/**
 *
 */
class SC_Helper_News_deleteNewsTest extends SC_Helper_News_TestBase
{

    protected function setUp()
    {
        parent::setUp();
        $this->objNews = new SC_Helper_News_Ex();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testDeleteNewsTest_ニュースIDを指定した場合_対象のニュース情報が削除される()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpNews();
        $news_id = 1002;

        $this->expected = '1';

        $this->objNews->deleteNews($news_id);

        $col = 'del_flg';
        $from = 'dtb_news';
        $where = 'news_id = ?';
        $whereVal = array($news_id);
        $res = $objQuery->getCol($col, $from, $where, $whereVal);
        $this->actual = $res[0];

        $this->verify();
    }
}

