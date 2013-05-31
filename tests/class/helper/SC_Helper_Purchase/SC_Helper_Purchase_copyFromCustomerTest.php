<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_TestBase.php");
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

/**
 * SC_Helper_Purchase::copyFromCustomer()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Helper_Purchase_copyFromCustomerTest extends SC_Helper_Purchase_TestBase
{

  var $customer;
  var $customer_array;

  protected function setUp()
  {
    parent::setUp();
    $this->customer = new SC_Customer();
    $this->customer->setValue('customer_id', '1001');
    $this->customer->setValue('name01', '姓01');
    $this->customer->setValue('name02', '名01');
    $this->customer->setValue('kana01', 'セイ01');
    $this->customer->setValue('kana02', 'メイ01');
    $this->customer->setValue('sex', '1');
    $this->customer->setValue('zip01', '123');
    $this->customer->setValue('zip02', '4567');
    $this->customer->setValue('pref', '東京都');
    $this->customer->setValue('addr01', 'abc市');
    $this->customer->setValue('addr02', 'def町');
    $this->customer->setValue('tel01', '01');
    $this->customer->setValue('tel02', '234');
    $this->customer->setValue('tel03', '5678');
    $this->customer->setValue('fax01', '02');
    $this->customer->setValue('fax02', '345');
    $this->customer->setValue('fax03', '6789');
    $this->customer->setValue('job', '会社員');
    $this->customer->setValue('birth', '2012-01-01');
    $this->customer->setValue('email', 'test@example.com');

    $this->customer_array = array('customer_id' => '1001', 'email' => 'test@example.com');
  }

  protected function tearDown()
  {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testCopyFromCustomer_ログインしていない場合_何もしない()
  {
    $dest = array();
    User_Utils::setLoginState(FALSE, $this->customer_array, $this->objQuery);

    $this->expected = array();
    $helper = new SC_Helper_Purchase();
    $helper->copyFromCustomer($dest, $this->customer);
    $this->actual = $dest;

    $this->verify();
  }

  public function testCopyFromCustomer_モバイルの場合_モバイルのメールアドレスを設定する()
  {
    $dest = array();
    User_Utils::setLoginState(TRUE, $this->customer_array, $this->objQuery);
    User_Utils::setDeviceType(DEVICE_TYPE_MOBILE);
    $this->customer->setValue('email_mobile', 'mobile@example.com');

    $this->expected = array(
      'order_name01' => '姓01',
      'order_name02' => '名01',
      'order_kana01' => 'セイ01',
      'order_kana02' => 'メイ01',
      'order_sex' => '1',
      'order_zip01' => '123',
      'order_zip02' => '4567',
      'order_pref' => '東京都',
      'order_addr01' => 'abc市',
      'order_addr02' => 'def町',
      'order_tel01' => '01',
      'order_tel02' => '234',
      'order_tel03' => '5678',
      'order_fax01' => '02',
      'order_fax02' => '345',
      'order_fax03' => '6789',
      'order_job' => '会社員',
      'order_birth' => '2012-01-01',
      'order_email' => 'mobile@example.com',
      'customer_id' => '1001',
      'update_date' => 'CURRENT_TIMESTAMP',
      'order_country_id' => ''
    );
    $helper = new SC_Helper_Purchase();
    $helper->copyFromCustomer($dest, $this->customer);
    $this->actual = $dest;

    $this->verify();
  }

  public function testCopyFromCustomer_モバイルかつモバイルのメールアドレスがない場合_通常のメールアドレスを設定する()
  {
    $dest = array();
    $prefix = 'order';
    // キーを絞る
    $keys = array('name01', 'email');
    User_Utils::setLoginState(TRUE, $this->customer_array, $this->objQuery);
    User_Utils::setDeviceType(DEVICE_TYPE_MOBILE);

    $this->expected = array(
      'order_name01' => '姓01',
      'order_email' => 'test@example.com',
      'customer_id' => '1001',
      'update_date' => 'CURRENT_TIMESTAMP'
    );
    $helper = new SC_Helper_Purchase();
    $helper->copyFromCustomer($dest, $this->customer, $prefix, $keys);
    $this->actual = $dest;

    $this->verify();
  }

  public function testCopyFromCustomer_モバイルでない場合_通常のメールアドレスをそのまま設定する()
  {
    $dest = array();
    $prefix = 'prefix';
    // キーを絞る
    $keys = array('name01', 'email');
    User_Utils::setLoginState(TRUE, $this->customer_array, $this->objQuery);
    User_Utils::setDeviceType(DEVICE_TYPE_PC);
    $this->customer->setValue('email_mobile', 'mobile@example.com');

    $this->expected = array(
      'prefix_name01' => '姓01',
      'prefix_email' => 'test@example.com',
      'customer_id' => '1001',
      'update_date' => 'CURRENT_TIMESTAMP'
    );
    $helper = new SC_Helper_Purchase();
    $helper->copyFromCustomer($dest, $this->customer, $prefix, $keys);
    $this->actual = $dest;

    $this->verify();
  }

  //////////////////////////////////////////

}

