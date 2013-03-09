<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_News/SC_Helper_News_TestBase.php");
/**
 *
 */
class SC_Helper_News_getCount extends SC_Helper_News_TestBase
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

    public function testGetCount_削除されたニュースも含む場合_すべてのニュース件数を取得()
    {   
        $this->setUpNews();
        $has_deleted = true;

        $this->expected = 4;

        $this->actual = $this->objNews->getCount($has_deleted);

        $this->verify();
    }

    public function testGetCount_削除されたニュースは含まない場合_削除されていないニュース件数を取得()
    {   
        $this->setUpNews();
        $has_deleted = false;

        $this->expected = 3;

        $this->actual = $this->objNews->getCount($has_deleted);

        $this->verify();
    }
}
