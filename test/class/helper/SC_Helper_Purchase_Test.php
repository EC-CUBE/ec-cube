<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

// {{{ requires
require_once(realpath(dirname(__FILE__)) . "/../../require.php");
require_once(realpath(dirname(__FILE__)) . "/../../../data/class_extends/helper_extends/SC_Helper_Purchase_Ex.php");

/**
 * SC_Helper_Purchase のテストケース.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_Helper_Purchase_Test extends PHPUnit_Framework_TestCase 
{
    /**
     * @var SC_Helper_Purchase
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new SC_Helper_Purchase;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @todo Implement testCompleteOrder().
     */
    public function testCompleteOrder()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testCancelOrder().
     */
    public function testCancelOrder()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRollbackOrder().
     */
    public function testRollbackOrder()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testVerifyChangeCart().
     */
    public function testVerifyChangeCart()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetOrderTemp().
     */
    public function testGetOrderTemp()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetOrderTempByOrderId().
     */
    public function testGetOrderTempByOrderId()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSaveOrderTemp().
     */
    public function testSaveOrderTemp()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * getShippingTemp() のテストケース.
     */
    public function testGetShippingTemp()
    {
        $_SESSION['shipping'] = array(
            '0' => array('shipping_name01' => 'test01', 'shipment_item' => array('10' => array('shipment_item' => 0) ) ),
            '1' => array('shipping_name01' => 'test02'),
            '2' => array('shipping_name01' => 'test03', 'shipment_item' => array('11' => array('shipment_item' => 1) ) ),
            '3' => array('shipping_name01' => 'test04', 'shipment_item' => array('12' => array('shipment_item' => 2) ) ),
        );
        
        // 第一引数(複数お届け先フラグ)がfalseの場合、$_SESSION['shipping']をそのまま返す
        $result = SC_Helper_Purchase_Ex::getShippingTemp(false);
        $this->assertEquals($result, $_SESSION['shipping']);
        
        // 第一引数(複数お届け先フラグ)がtrueの場合、実際に配送で利用されるお届け先の情報のみを入れたデータを返す
        $result = SC_Helper_Purchase_Ex::getShippingTemp(true);
        $this->assertEquals(false, $result === $_SESSION['shipping']);
        $this->assertEquals(false, $result == $_SESSION['shipping']);
        $this->assertEquals(3, count($result));
        
        unset($_SESSION['shipping']);
    }

    /**
     * @todo Implement testClearShipmentItemTemp().
     */
    public function testClearShipmentItemTemp()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSetShipmentItemTemp().
     */
    public function testSetShipmentItemTemp()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetShippingPref().
     */
    public function testGetShippingPref()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testIsMultiple().
     */
    public function testIsMultiple()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSaveShippingTemp().
     */
    public function testSaveShippingTemp()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testUnsetShippingTemp().
     */
    public function testUnsetShippingTemp()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testCopyFromCustomer().
     */
    public function testCopyFromCustomer()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testCopyFromOrder().
     */
    public function testCopyFromOrder()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetPaymentsByPrice().
     */
    public function testGetPaymentsByPrice()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetDelivDate().
     */
    public function testGetDelivDate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetDateArray().
     */
    public function testGetDateArray()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetDelivTime().
     */
    public function testGetDelivTime()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetDeliv().
     */
    public function testGetDeliv()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetPayments().
     */
    public function testGetPayments()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRegisterShipping().
     */
    public function testRegisterShipping()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRegisterShipmentItem().
     */
    public function testRegisterShipmentItem()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRegisterOrderComplete().
     */
    public function testRegisterOrderComplete()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRegisterOrder().
     */
    public function testRegisterOrder()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testRegisterOrderDetail().
     */
    public function testRegisterOrderDetail()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetOrder().
     */
    public function testGetOrder()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetOrderDetail().
     */
    public function testGetOrderDetail()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSetDownloadableFlgTo().
     */
    public function testSetDownloadableFlgTo()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetShippings().
     */
    public function testGetShippings()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetShipmentItems().
     */
    public function testGetShipmentItems()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSendOrderMail().
     */
    public function testSendOrderMail()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSfUpdateOrderStatus().
     */
    public function testSfUpdateOrderStatus()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSfUpdateOrderNameCol().
     */
    public function testSfUpdateOrderNameCol()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testIsUsePoint().
     */
    public function testIsUsePoint()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testIsAddPoint().
     */
    public function testIsAddPoint()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testCleanupSession().
     */
    public function testCleanupSession()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
?>
