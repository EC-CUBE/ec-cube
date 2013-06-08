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
 * SC_Helper_BestProducts::deleteBestProducts()のテストクラス.
 *
 * @author hiroshi kakuta
 */
class SC_Helper_BestProducts_deleteBestProductsTest extends SC_Helper_BestProducts_TestBase
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

    // データが削除されていることを確認
    public function testDeleteBestProducts_データが削除される(){

        SC_Helper_BestProducts_Ex::deleteBestProducts("1001");

        $this->expected = null;

        $this->actual = SC_Helper_BestProducts_Ex::getBestProducts('1001');

        $this->verify();
    }

    // データが削除後、ランクが繰り上がることを確認
    public function testDeleteBestProducts_データ削除後_ランクが繰り上がることを確認(){

        SC_Helper_BestProducts_Ex::deleteBestProducts("1001");

        $this->expected = "1";

        $arrRet = SC_Helper_BestProducts_Ex::getBestProducts('1002',true);

        $this->actual = $arrRet['rank'];

        $this->verify();
    }
}

