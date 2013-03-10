<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_BestProducts/SC_Helper_BestProducts_TestBase.php");
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
 *
 * @author hiroshi kakuta
 */
class SC_Helper_BestProducts_rankUpTest extends SC_Helper_BestProducts_TestBase
{
    protected function setUp()
    {
        parent::setUp();
        $this->setUpBestProducts();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testRankUp_指定されたデータがランクアップされる(){

        SC_Helper_BestProducts_Ex::rankUp("1003");

        $this->expected = "2";

        $arrRet = SC_Helper_BestProducts_Ex::getBestProducts('1003');

        $this->actual = $arrRet['rank'];

        $this->verify();
    }

}

