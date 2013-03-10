<?php

$HOME = realpath(dirname(__FILE__)) . "/../../../..";
require_once($HOME . "/tests/class/helper/SC_Helper_DB/SC_Helper_DB_TestBase.php");
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
 * SC_Helper_DB::sfColumnExists()のテストクラス.
 *
 * @author Hiroko Tamagawa
 * @version $Id: SC_Helper_DB_sfColumnExists.php 22567 2013-02-18 10:09:54Z shutta $
 */
class SC_Helper_DB_sfColumnExists extends SC_Helper_DB_TestBase
{

    protected function setUp()
    {
        parent::setUp();
        $this->helper = new SC_Helper_DB_sfColumnExistsMock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////
    public function testSfColumnExists_指定のカラムが存在する場合_TRUEを返す()
    {
        $tableName = 'dtb_news';
        $colName = 'news_id';
        $this->expected = true;
        $this->actual = $this->helper->sfColumnExists($tableName, $colName);
        $this->verify();
    }
    
    public function testSfColumnExists_指定のカラムが存在しない場合_FALSEを返す()
    {
        $tableName = 'dtb_news';
        $colName = 'news_id2';
        $this->expected = false;
        $this->actual = $this->helper->sfColumnExists($tableName, $colName);
        $this->verify();
    }

    public function testSfColumnExists_指定のカラムが存在しない場合追加して_TRUEを返す()
    {
        $tableName = 'dtb_news';
        $colName = 'news_id2';
        $colType = 'int';
        $dsn = '';
        $add = true;
        $this->expected = true;
        $this->actual = $this->helper->sfColumnExists($tableName, $colName, $colType, $dsn, $add);
        $this->verify();
    }

}

class SC_Helper_DB_sfColumnExistsMock extends SC_Helper_DB_Ex
{
    function sfColumnAdd($tableName, $colName, $colType) {
        return true;
    }
}


