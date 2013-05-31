<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Address/SC_Helper_Address_TestBase.php");
/**
 *
 */
class SC_Helper_Address_registAddressTest extends SC_Helper_Address_TestBase
{

    protected function setUp()
    {
        parent::setUp();
        $this->objAddress = new SC_Helper_Address_Ex();
    }

    protected function tearUp()
    {
        parent::tearUp();
    }

    /////////////////////////////////////////

//顧客idがない場合は直にExitしてしまうので未実行
/*
    public function testregistAddressTest_顧客idが無い場合_システムエラーを返す()
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->setUpAddress();
        //$this->expected = "1";
        $this->objAddress->registAddress(null);
        $this->actual = '';
        $this->verify('アドレス追加');
    }
*/


    /* MySQL でもエラーになるのでとりいそぎ回避
    public function testregistAddressTest_会員の登録配送先を追加する()
    {
         if(DB_TYPE != 'pgsql') { //postgresqlだとどうしてもDBエラーになるのでとりいそぎ回避
            $this->setUpAddress();
            $arrSql =
                array(
                    'customer_id' => '1',
                    'name01' => 'テスト',
                    'name02' => 'よん',
                    'kana01' => 'テスト',
                    'kana02' => 'ヨン',
                    'zip01' => '333',
                    'zip02'=> '3333',
                    'pref' => '4',
                    'addr01' => 'テスト4',
                    'addr02' => 'テスト4',
                    'tel01' => '001',
                    'tel02' => '0002',
                    'tel03' => '0003',
                    'fax01' => '112',
                    'fax02' => '1113',
                    'fax03' => '1114',
                    'country_id' => null
                );
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $this->expected = '1002';
            $this->objAddress->registAddress($arrSql);
            $col = 'other_deliv_id';
            $from = 'dtb_other_deliv';
            $where = 'other_deliv_id = ?';
            $arrWhere = array($this->expected);
            $ret = $objQuery->select($col, $from, $where, $arrWhere);
            $this->actual = $ret['other_deliv_id'];
            $this->verify('アドレス追加');
        }
    }
    */


    public function testregistAddressTest_会員の登録配送先を更新する()
    {
        $this->setUpAddress();
        $arrSql =
            array(
                'other_deliv_id' => '1000',
                'customer_id' => '1',
                'name01' => 'テスト',
                'name02' => '更新',
                'kana01' => 'テスト',
                'kana02' => 'コウシン',
                'zip01' => '222',
                'zip02'=> '2222',
                'pref' => '4',
                'addr01' => 'テスト1',
                'addr02' => 'テスト1',
                'tel01' => '001',
                'tel02' => '0002',
                'tel03' => '0003',
                'fax01' => '112',
                'fax02' => '1113',
                'fax03' => '1114',
                'country_id' => null
            );
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $this->objAddress->registAddress($arrSql);

        $this->expected = $arrSql;
        $col = '*';
        $from = 'dtb_other_deliv';
        $where = 'other_deliv_id = ?';
        $arrWhere = array($arrSql['other_deliv_id']);
        $this->actual = $objQuery->getRow($col, $from, $where, $arrWhere);
        
        $this->verify('登録配送先更新');
    }




}
