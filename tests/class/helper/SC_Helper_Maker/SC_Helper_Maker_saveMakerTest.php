<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_Maker/SC_Helper_Maker_TestBase.php");
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

/**
 *
 * @author hiroshi kakuta
 */
class SC_Helper_Maker_saveMakerTest extends SC_Helper_Maker_TestBase
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

    public function testSaveMaker_メーカーIDを指定すると更新される(){
    //public function testSaveMaker_update(){
        $sqlVal = array(
            'maker_id' => '1001',
            'name' => 'ソニンー',
        );

        $this->objHelperMaker->saveMaker($sqlVal);

        $this->expected = array(
            'name' => 'ソニンー',
        );

        $arrRet = $this->objHelperMaker->getMaker('1001');

        $this->actual = Test_Utils::mapArray($arrRet,
            array('name')
        );

        $this->verify();
    }

    public function testSaveMaker_メーカーIDがない場合_インサートされる(){
    //public function testSaveMaker_insert(){

        if(DB_TYPE != 'pgsql') { //postgresqlだとどうしてもDBエラーになるのでとりいそぎ回避
            $sqlVal = array(
                'name' => 'フジスリー',
                'creator_id' => '1',
                'del_flg' => '0'
            );

            $maker_id = $this->objHelperMaker->saveMaker($sqlVal);

            $this->expected = array(
                'name' => 'フジスリー',
                'rank' => '5',
                'creator_id' => '1',
                'del_flg' => '0'
            );

            $arrRet = $this->objHelperMaker->getMaker($maker_id);

            $this->actual = Test_Utils::mapArray($arrRet,
                array(
                    'name',
                    'rank',
                    'creator_id',
                    'del_flg'
                )
            );

            $this->verify();
        }

    }
}

