<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Purchase/SC_Helper_Purchase_TestBase.php");
/**
 *
 */
class SC_Helper_Purchase_registerShippingTempTest extends SC_Helper_Purchase_TestBase {

  protected function setUp() {
    parent::setUp();
    $this->setUpShippingOnDb();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function testRegisterShipping_元々存在しない受注IDの場合_新規にデータが作られる() {
    $order_id = '10';
    $arrParams = array(
        '20' =>
        array(
            'order_id' => '10',
            'shipping_id' => '20',
            'shipping_name01' => '配送情報10',
            'shipping_date' => '2012/01/12'
       )
    );
 
    $this->expected['count'] = '3'; // 1件増える
    $this->expected['content'] = array(
        'order_id' => '10',
        'shipping_id' => '20',
        'shipping_name01' => '配送情報10',
        'shipping_date' => '2012-01-12 00:00:00'
    );

    SC_Helper_Purchase::registerShipping($order_id, $arrParams);
    
    $this->actual['count'] = $this->objQuery->count('dtb_shipping');
    $this->result = $this->objQuery->setWhere('order_id = ?')
                                             ->select(
        'order_id,shipping_id,shipping_name01,shipping_date,create_date,update_date',
        'dtb_shipping',
        '',
        array($order_id));
    $this->actual['content'] = $this->result[0];
    unset($this->actual['content']['create_date']);
    unset($this->actual['content']['update_date']);
    $this->verify('登録した配送情報');
    $this->assertNotNull($this->result[0]['create_date']);
    $this->assertNotNull($this->result[0]['update_date']);
  }

  public function testRegisterShipping_元々存在する受注IDの場合_既存のデータが置き換えられる() {
    $order_id = '2';
    $arrParams = array(
        '30' =>
        array(
            'order_id' => '2',
            'shipping_id' => '30',
            'shipping_name01' => '配送情報02-update',
            'shipping_date' => '2013/12/03'
        )
    );

    $this->expected['count'] = '2'; // 件数が変わらない
    $this->expected['content'] = array(
        'order_id' => '2',
        'shipping_id' => '30',
        'shipping_name01' => '配送情報02-update',
        'shipping_date' => '2013-12-03 00:00:00'
    );

    SC_Helper_Purchase::registerShipping($order_id, $arrParams);
    
    $this->actual['count'] = $this->objQuery->count('dtb_shipping');
    $this->result = $this->objQuery->setWhere('order_id = ?')
                                              ->select(
        'order_id,shipping_id,shipping_name01,shipping_date,create_date,update_date',
        'dtb_shipping',
        '',
        array($order_id));
    $this->actual['content'] = $this->result[0];
    unset($this->actual['content']['create_date']);
    unset($this->actual['content']['update_date']);
    $this->verify('登録した配送情報');
    $this->assertNotNull($this->result[0]['create_date']);
    $this->assertNotNull($this->result[0]['update_date']);
  }

  public function testRegisterShipping_配送日付が空の場合_エラーが起きず変換処理がスキップされる() {
    $order_id = '2';
    $arrParams = array(
        '30' =>
        array(
            'order_id' => '2',
            'shipping_id' => '30',
            'shipping_name01' => '配送情報02-update'
    //      'shipping_date' => '2013/12/03 00:00:00'
        )
    );

    $this->expected['count'] = '2';
    $this->expected['content'] = array(
        'order_id' => '2',
        'shipping_id' => '30',
        'shipping_name01' => '配送情報02-update',
        'shipping_date' => NULL
    );

    SC_Helper_Purchase::registerShipping($order_id, $arrParams);
    
    $this->actual['count'] = $this->objQuery->count('dtb_shipping');
    $this->result = $this->objQuery->setWhere('order_id = ?')
                                              ->select(
        'order_id,shipping_id,shipping_name01,shipping_date,create_date,update_date',
        'dtb_shipping',
        '',
        array($order_id));
    $this->actual['content'] = $this->result[0];
    unset($this->actual['content']['create_date']);
    unset($this->actual['content']['update_date']);
    $this->verify('登録した配送情報');
    $this->assertNotNull($this->result[0]['create_date']);
    $this->assertNotNull($this->result[0]['update_date']);
  }

  public function testRegisterShipping_非会員購入の場合_配送IDが設定される() {
    $order_id = '2';
    $arrParams = array(
        '30' =>
        array(
            'order_id' => '2',
        //    'shipping_id' => '30',
            'shipping_name01' => '配送情報02-update',
            'shipping_date' => '2013/12/03 00:00:00'
        )
    );

    $this->expected['count'] = '2'; // 件数が変わらない
    $this->expected['content'] = array(
        'order_id' => '2',
        'shipping_id' => '30',
        'shipping_name01' => '配送情報02-update',
        'shipping_date' => '2013-12-03 00:00:00'
    );

    SC_Helper_Purchase::registerShipping($order_id, $arrParams);
    
    $this->actual['count'] = $this->objQuery->count('dtb_shipping');
    $this->result = $this->objQuery->setWhere('order_id = ?')
                                              ->select(
        'order_id,shipping_id,shipping_name01,shipping_date,create_date,update_date',
        'dtb_shipping',
        '',
        array($order_id));
    $this->actual['content'] = $this->result[0];
    unset($this->actual['content']['create_date']);
    unset($this->actual['content']['update_date']);
    $this->verify('登録した配送情報');
    $this->assertNotNull($this->result[0]['create_date']);
    $this->assertNotNull($this->result[0]['update_date']);
  }

  //////////////////////////////////////////

}

