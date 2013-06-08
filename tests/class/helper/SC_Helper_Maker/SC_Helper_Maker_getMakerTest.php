<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Maker/SC_Helper_Maker_TestBase.php");
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
 * SC_Helper_Maker::getMaker()のテストクラス.
 *
 * @author hiroshi kakuta
 */
class SC_Helper_Maker_getMakerTest extends SC_Helper_Maker_TestBase
{

    var $objHelperMaker;

    protected function setUp()
    {
        parent::setUp();
        $this->setUpMaker();
        $this->objHelperMaker = new SC_Helper_Maker_Ex();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /** maker_idが存在しない場合、空を返す。
     */
    public function testGetMaker_おすすめidが存在しない場合_空を返す()
    {
        $this->expected = null;

        $this->actual = $this->objHelperMaker->getMaker("9999");

        $this->verify();
    }

    // maker_idが存在する場合、対応した結果を取得できる。
    public function testGetMaker_メーカーIDが存在する場合_対応した結果を取得できる(){


        $this->expected = array(
            'maker_id' => '1001',
            'name' => 'ソニン',
            'rank' => '1',
            'creator_id' => '1',
            'create_date' => '2000-01-01 00:00:00',
            'update_date' => '2000-01-01 00:00:00',
            'del_flg' => '0'
        );

        $result = $this->objHelperMaker->getMaker("1001");

        $this->actual = Test_Utils::mapArray($result,
            array('maker_id',
                'name',
                'rank',
                'creator_id',
                'create_date',
                'update_date',
                'del_flg'
            ));

        $this->verify();
    }

    public function testGetMaker_おすすめIDがあり_かつ削除済みの場合_空が返る(){

        $this->expected = null;

        $result = $this->objHelperMaker->getMaker("1002");

        $this->verify();
    }

    // best_idが存在するが、del_flg=1の場合、かつ。$has_deleted=trueを指定
    public function testGetMaker_削除済みでかつhas_deletedがtrueの場合_対応した結果が返る(){

        $best_id = '1002';

        $this->expected = array(
            'maker_id' => '1002',
            'name' => 'パソナニック',
            'rank' => '2',
            'creator_id' => '2',
            'create_date' => '2000-01-01 00:00:00',
            'update_date' => '2000-01-01 00:00:00',
            'del_flg' => '1'
        );

        $result = $this->objHelperMaker->getMaker("1002",true);

        $this->actual = Test_Utils::mapArray($result,
            array('maker_id',
                'name',
                'rank',
                'creator_id',
                'create_date',
                'update_date',
                'del_flg'
            ));

        $this->verify();
    }
}

