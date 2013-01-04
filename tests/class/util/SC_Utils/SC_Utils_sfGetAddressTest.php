<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
 * SC_Utils::sfGetAddress()のテストクラス.
 *
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class SC_Utils_sfGetAddressTest extends Common_TestCase {


  protected function setUp() {
    parent::setUp();
    $this->setUpAddress();
  }

  protected function tearDown() {
    parent::tearDown();
  }

  /////////////////////////////////////////
  public function test_住所がヒットしない場合_空の配列が返る() {
    $this->expected = array();
    $this->actual = SC_Utils::sfGetAddress('9999999');

    $this->verify('郵便番号検索結果');
  }

  public function test_住所が一件だけヒットする場合_住所データが取得できる() {
    $this->expected = array(
      array(
        'state' => '1',    // 北海道
        'city' => '札幌市中央区',
        'town' => '大通東'
      )
    );
    $this->actual = SC_Utils::sfGetAddress('0600041');

    $this->verify('郵便番号検索結果');
  }

  // TODO 二件目に関しては件名のIDへの変換と町名の削除が行われない。
  // 今の仕様ではこれでOKかもしれないが、そもそも一件目しか使わないのなら
  // $data_list[0]を返した方が良いのでは?
  public function test_住所が二件以上ヒットする場合_町名を消した住所データが取得できる() {
    $this->expected = array(
      array(
        'state' => '5',    // 秋田県
        'city' => '秋田市',
        'town' => ''
      ),
      array(
        'state' => '5',
        'city' => '秋田市',
        'town' => ''
      )
    );
    $this->actual = SC_Utils::sfGetAddress('0110951');

    $this->verify('郵便番号検索結果');
  }

  public function test_住所に但し書きが含まれる場合_但し書きが消去される() {
    $this->expected = array(
      array(
        'state' => '1',    // 北海道
        'city' => '札幌市中央区',
        'town' => '大通西'
      )
    );
    $this->actual = SC_Utils::sfGetAddress('0600042');

    $this->verify('郵便番号検索結果');
  }

  public function test_住所に注意文言がある場合_町名が消去される() {
    $this->expected = array(
      array(
        'state' => '1',    // 北海道
        'city' => '札幌市中央区',
        'town' => ''
      )
    );
    $this->actual = SC_Utils::sfGetAddress('0600000');

    $this->verify('郵便番号検索結果');
  }

  public function test_住所に番地の説明が含まれる場合_町名が消去される() {
    $this->expected = array(
      array(
        'state' => '8',    // 茨城県
        'city' => '猿島郡堺町',
        'town' => ''
      )
    );
    $this->actual = SC_Utils::sfGetAddress('3060433');

    $this->verify('郵便番号検索結果');
  }

  //////////////////////////////////////////

  protected function setUpAddress() {

    $address = array(
      array(
        'zip_id' => '2',
        'zipcode' => '0600041',
        'state' => '北海道',
        'city' => '札幌市中央区',
        'town' => '大通東'
      ),
      array(
        'zip_id' => '3',
        'zipcode' => '0600042',
        'state' => '北海道',
        'city' => '札幌市中央区',
        'town' => '大通西（１〜１９丁目）'
      ),
      array(
        'zip_id' => '0',
        'zipcode' => '0600000',
        'state' => '北海道',
        'city' => '札幌市中央区',
        'town' => '以下に掲載がない場合'
      ),
      array(
        'zip_id' => '26867',
        'zipcode' => '3060433',
        'state' => '茨城県',
        'city' => '猿島郡堺町',
        'town' => '堺町の次に番地がくる場合'
      ),
      array(
        'zip_id' => '16223',
        'zipcode' => '0110951',
        'state' => '秋田県',
        'city' => '秋田市',
        'town' => '土崎港相染町'
      ),
      array(
        'zip_id' => '16226',
        'zipcode' => '0110951',
        'state' => '秋田県',
        'city' => '秋田市',
        'town' => '土崎港古川町'
      )
    );

    $this->objQuery->delete('mtb_zip');
    foreach ($address as $item) {
      $this->objQuery->insert('mtb_zip', $item);
    }
  }
}

