<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");
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
 * SC_Helper_Makerのテストの基底クラス.
 *
 *
 * @author hiroshi kakuta
 * @version $Id$
 */
class SC_Helper_Maker_TestBase extends Common_TestCase
{

    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }


    /**
     * DBにお勧め情報を登録します.
     */
    protected function setUpMaker()
    {
        $makers = array(
            array(
                'maker_id' => '1001',
                'name' => 'ソニン',
                'rank' => '1',
                'creator_id' => '1',
                'create_date' => '2000-01-01 00:00:00',
                'update_date' => '2000-01-01 00:00:00',
                'del_flg' => '0'
            ),
            array(
                'maker_id' => '1002',
                'name' => 'パソナニック',
                'rank' => '2',
                'creator_id' => '2',
                'create_date' => '2000-01-01 00:00:00',
                'update_date' => '2000-01-01 00:00:00',
                'del_flg' => '1'
            ),
            array(
                'maker_id' => '1003',
                'name' => 'シャンプー',
                'rank' => '3',
                'creator_id' => '1',
                'create_date' => '2000-01-01 00:00:00',
                'update_date' => '2000-01-01 00:00:00',
                'del_flg' => '0'
            ),
            array(
                'maker_id' => '1004',
                'name' => 'MEC',
                'rank' => '4',
                'creator_id' => '1',
                'create_date' => '2000-01-01 00:00:00',
                'update_date' => '2000-01-01 00:00:00',
                'del_flg' => '0'
            )
        );

        $this->objQuery->delete('dtb_maker');
        foreach ($makers as  $item) {
            $this->objQuery->insert('dtb_maker', $item);
        }
    }

    protected function deleteAllMaker(){
        $this->objQuery->delete('dtb_maker');
    }
}

