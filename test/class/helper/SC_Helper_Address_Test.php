<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SC_Helper_Address_Test
 *
 * @author cyberwill
 */
class SC_Helper_Address_Test extends PHPUnit_Framework_TestCase
{
    public $objQuery = "";
    public $objAddress = "";
    public $customer_id = "";

    public $dummy = array(
        'name01' => '追加',
        'name02' => '住所',
        'kana01' => 'ツイカ',
        'kana02' => 'ジュウショ',
        'zip01' => '123',
        'zip02' => '4567',
        'pref' => '23',
        'addr01' => 'その他のお届け先',
        'addr02' => '',
        'tel01' => '0123',
        'tel02' => '4567',
        'tel03' => '8901',
        'fax01' => '',
        'fax02' => '',
        'fax03' => '',
    );

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->objQuery =& SC_Query::getSingletonInstance();
        $this->objQuery->begin();
        $arrRet = $this->objQuery->getOne('SELECT MAX(customer_id) FROM dtb_customer');
        $this->customer_id = $arrRet;
        $this->objAddress = new SC_Helper_Address_Ex();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->objQuery->rollback();
    }

    function testRegistAddress() {
        // ダミーの住所を登録
        $sqlval = $this->dummy;
        $sqlval['customer_id'] = $this->customer_id;
        // 検証用の住所02
        $sqlval['addr02'] = $create_test = 'create test ' . time();
        $this->objAddress->registAddress($sqlval);

        // 住所02が検証用のものと同じか確認
        $this->objQuery->setOrder('other_deliv_id DESC');
        $created_address = $this->objQuery->getRow('other_deliv_id, addr02', 'dtb_other_deliv', 'customer_id = ?', array($this->customer_id));
        $this->assertEquals($create_test, $created_address['addr02']);

        $sqlval['other_deliv_id'] = $created_address['other_deliv_id'];
        // 更新の検証のために住所02を変更
        $sqlval['addr02'] = $update_test = 'update test ' . time();
        $this->objAddress->registAddress($sqlval);

        // 住所02が検証用のものと同じか検証
        $this->objQuery->setOrder('other_deliv_id DESC');
        $updated_address = $this->objQuery->getRow('addr02', 'dtb_other_deliv', 'other_deliv_id = ?', array($created_address['other_deliv_id']));
        $this->assertEquals($update_test, $updated_address['addr02']);
    }

    /**
     * @depends testSave
     */
    function testGetAddress() {
        // testSave のテストが通っていること前提
        $sqlval = $this->dummy;
        $sqlval['customer_id'] = $this->customer_id;
        $sqlval['addr02'] = 'get test';
        $this->objAddress->registAddress($sqlval);
        $this->objQuery->setOrder('other_deliv_id DESC');
        $other_deliv_id = $this->objQuery->getOne('SELECT other_deliv_id FROM dtb_other_deliv WHERE customer_id = ?', array($this->customer_id));
        // DBに正しく記録され、取得できているか確認
        $address = $this->objAddress->getAddress($other_deliv_id);
        $result = TRUE;
        foreach ($sqlval as $key => $value) {
            if ($value != $address[$key]) {
                $result = FALSE;
            }
        }
        $this->assertTrue($result);
    }

    /**
     * @depends testSave
     */
    function testGetList() {
        // testSave のテストが通っていること前提
        $sqlval = $this->dummy;
        $sqlval['customer_id'] = $this->customer_id;
        $sqlval['addr02'] = 'getList test';
        $this->objAddress->registAddress($sqlval);
        $list = $this->objAddress->getList($this->customer_id);
        $found = FALSE;
        foreach ($list as $address) {
            $check = TRUE;
            foreach ($sqlval as $key => $value) {
                if ($value != $address[$key]) {
                    $check = FALSE;
                }
            }
            if ($check) {
                $found = TRUE;
                break;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * @depends testSave
     */
    function testDeleteAddress() {
        // testSave のテストが通っていること前提
        $sqlval = $this->dummy;
        $sqlval['customer_id'] = $this->customer_id;
        $sqlval['addr02'] = 'delete test';
        $this->objAddress->registAddress($sqlval);
        $this->objQuery->setOrder('other_deliv_id DESC');
        $other_deliv_id = $this->objQuery->getOne('SELECT other_deliv_id FROM dtb_other_deliv WHERE customer_id = ?', array($this->customer_id));
        $this->objAddress->deleteAddress($other_deliv_id);
        $result = $this->objQuery->getRow('*', 'dtb_other_deliv', 'customer_id = ? and other_deliv_id = ?', array($this->customer_id, $other_deliv_id));
        $this->assertNull($result);
    }
}
