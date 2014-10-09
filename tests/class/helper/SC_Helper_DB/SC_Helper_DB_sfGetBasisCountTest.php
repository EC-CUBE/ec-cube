<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_DB/SC_Helper_DB_TestBase.php");
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
 * SC_Helper_DB::sfGetBasisCount()のテストクラス.
 *
 * @author Hiroko Tamagawa
 * @version $Id: SC_Helper_DB_sfGetBasisCount.php 22567 2013-02-18 10:09:54Z shutta $
 */
class SC_Helper_DB_sfGetBasisCount extends SC_Helper_DB_TestBase
{

    protected function setUp()
    {
        parent::setUp();
        $this->helper = new SC_Helper_DB_Ex();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////
    public function testSfGetBasisCount_baseinfoのデータが1行の場合_1を返す()
    {
        $this->setUpBasisData();
        $this->expected = 1;
        $this->actual = $this->helper->sfGetBasisCount();
        $this->verify();
    }
    
    public function testSfGetBasisCount_baseinfoのデータが2行の場合_2を返す()
    {
        $this->setUpBasisData();
        $baseinfo = array(
            'id' => 2,
            'update_date' => 'CURRENT_TIMESTAMP'
            );
        $this->objQuery->insert('dtb_baseinfo', $baseinfo);
        $this->expected = 2;
        $this->actual = $this->helper->sfGetBasisCount();
        $this->verify();
    }
}
