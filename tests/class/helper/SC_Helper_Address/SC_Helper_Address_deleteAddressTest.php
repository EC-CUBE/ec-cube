<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Address/SC_Helper_Address_TestBase.php");
/**
 *
 */
class SC_Helper_Address_deleteAddressTest extends SC_Helper_Address_TestBase
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

    public function testdeleteAddressTest_会員の登録配送先を削除する()
    {
        $this->setUpAddress();
        $other_deliv_id = '1000';
        $this->expected = NULL;
        $this->objAddress->deleteAddress($other_deliv_id);
        $objQuery   =& SC_Query_Ex::getSingletonInstance();
        $select = '*';
        $from = 'dtb_other_deliv';
        $where = 'other_deliv_id = ?';
        $whereVal = array($other_deliv_id);
        $this->actual = $objQuery->getRow($select, $from, $where, $whereVal);
        
        $this->verify('登録配送先削除');
    }
}
