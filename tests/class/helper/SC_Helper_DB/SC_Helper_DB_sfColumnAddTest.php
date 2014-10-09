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
 * SC_Helper_DB::sfColumnAdd()のテストクラス.
 *
 * @author Hiroko Tamagawa
 * @version $Id: SC_Helper_DB_sfColumnAdd.php 22567 2013-02-18 10:09:54Z shutta $
 */
class SC_Helper_DB_sfColumnAdd extends SC_Helper_DB_TestBase
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
    public function testSfColumnAdd_指定のカラムが追加できたら_TRUEを返す()
    {
        $tableName = 'dtb_news';
        $colName = 'news_id2';
        $colType = 'int';
        $this->expected = true;
        $this->helper->sfColumnAdd($tableName, $colName, $colType);
        $columns = $this->objQuery->listTableFields($tableName);
        $this->actual = in_array($colName, $columns);
        // rolbackできないのでカラムを削除する
        $this->objQuery->query("ALTER TABLE $tableName DROP $colName");
        $this->verify();
    }
}
