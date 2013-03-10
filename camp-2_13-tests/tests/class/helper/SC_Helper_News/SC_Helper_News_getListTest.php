<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_News/SC_Helper_News_TestBase.php");
/**
 *
 */
class SC_Helper_News_getListTest extends SC_Helper_News_TestBase
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

    public function testGetList_削除されたニュースも含む場合_すべてのニュース一覧が取得できる()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpNews();
        $dispNumber = 0;
        $pageNumber = 0;
        $has_deleted = true;
        
        $this->expected = array(
          array(
            'update_date' => '2000-01-01 00:00:00',
            'news_id' => '1004',
            'news_title' => 'ニュース情報04',
            'creator_id' => '1',
            'del_flg' => '0'
            ),
          array(
            'update_date' => '2000-01-01 00:00:00',
            'news_id' => '1003',
            'news_title' => 'ニュース情報03',
            'creator_id' => '1',
            'del_flg' => '1'
            ),
          array(
            'update_date' => '2000-01-01 00:00:00',
            'news_id' => '1002',
            'news_title' => 'ニュース情報02',
            'creator_id' => '1',
            'del_flg' => '0'
            ),
          array(
            'update_date' => '2000-01-01 00:00:00',
            'news_id' => '1001',
            'news_title' => 'ニュース情報01',
            'creator_id' => '1',
            'del_flg' => '0'
            )
          );
        $result = $this->objNews->getList($dispNumber, $pageNumber, $has_deleted);
        foreach($result as $value) {
            $this->actual[] = Test_Utils::mapArray($value, array('update_date', 'news_id', 'news_title', 'creator_id', 'del_flg'));
        }

        $this->verify();
    }
    
    public function testGetList_削除されたニュースは含まない場合_削除されていないニュース一覧が取得できる()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpNews();
        $dispNumber = 0;
        $pageNumber = 0;
        $has_deleted = false;

        $this->expected = array(
          array(
            'update_date' => '2000-01-01 00:00:00',
            'news_id' => '1004',
            'news_title' => 'ニュース情報04',
            'creator_id' => '1',
            'del_flg' => '0'
            ),
          array(
            'update_date' => '2000-01-01 00:00:00',
            'news_id' => '1002',
            'news_title' => 'ニュース情報02',
            'creator_id' => '1',
            'del_flg' => '0'
            ),
          array(
            'update_date' => '2000-01-01 00:00:00',
            'news_id' => '1001',
            'news_title' => 'ニュース情報01',
            'creator_id' => '1',
            'del_flg' => '0'
            )
          );

        $result = $this->objNews->getList($dispNumber, $pageNumber, $has_deleted);
        foreach($result as $value) {
            $this->actual[] = Test_Utils::mapArray($value, array('update_date', 'news_id', 'news_title', 'creator_id', 'del_flg'));
        }

        $this->verify();
    }

    public function testGetList_表示件数1かつページ番号3の場合_対象のニュースが取得できる()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpNews();
        $dispNumber = 1;
        $pageNumber = 3;
        $has_deleted = false;

        $this->expected = array(
          'update_date' => '2000-01-01 00:00:00',
          'news_id' => '1001',
          'news_title' => 'ニュース情報01',
          'creator_id' => '1',
          'del_flg' => '0'
        );

        $result = $this->objNews->getList($dispNumber, $pageNumber, $has_deleted);
        $this->actual = Test_Utils::mapArray($result[0], array('update_date', 'news_id', 'news_title', 'creator_id', 'del_flg'));

        $this->verify();
    }

    public function testGetList_表示件数1かつページ番号0の場合_対象のニュースが取得できる()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpNews();
        $dispNumber = 1;
        $pageNumber = 0;
        $has_deleted = false;

        $this->expected = array(
          'update_date' => '2000-01-01 00:00:00',
          'news_id' => '1004',
          'news_title' => 'ニュース情報04',
          'creator_id' => '1',
          'del_flg' => '0'
        );

        $result = $this->objNews->getList($dispNumber, $pageNumber, $has_deleted);
        $this->actual = Test_Utils::mapArray($result[0], array('update_date', 'news_id', 'news_title', 'creator_id', 'del_flg'));

        $this->verify();
    }
}
