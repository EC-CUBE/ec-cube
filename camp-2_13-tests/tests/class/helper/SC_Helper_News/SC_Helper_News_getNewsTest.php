<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_News/SC_Helper_News_TestBase.php");
/**
 *
 */
class SC_Helper_News_getNewsTest extends SC_Helper_News_TestBase
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

    public function testGet_存在しないニュースIDを指定した場合_結果が空になる()
    {
        $this->setUpNews();
        $news_id = '9999';
        
        $this->expected = null;

        $this->actual = $this->objNews->getNews($news_id);

        $this->verify();
    }
    
    public function testGet_存在するニュースIDを指定した場合_対応する結果が取得できる()
    {
        $this->setUpNews();
        $news_id = '1001';

        $this->expected = array(
        'update_date' => '2000-01-01 00:00:00',
        'news_id' => '1001',
        'news_title' => 'ニュース情報01',
        'creator_id' => '1',
        'del_flg' => '0'
        );

        $result = $this->objNews->getNews($news_id);
        $this->actual = Test_Utils::mapArray($result, array('update_date', 'news_id', 'news_title', 'creator_id', 'del_flg'));

        $this->verify();
    }
}
