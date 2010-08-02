<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once(realpath(dirname(__FILE__)) . '/../../require.php');
require_once(realpath(dirname(__FILE__)) . '/../../../data/class/pages/LC_Page.php');

/**
 * SC_Utils のテストケース.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Test.php 15116 2007-07-23 11:32:53Z nanasess $
 */
class SC_Utils_Test extends PHPUnit_Framework_TestCase {

    // }}}
    // {{{ functions

    /**
     * SC_Utils::getRealURL() のテストケース.
     *
     * 変換無し
     */
    function testGetRealURL_変換無し() {
        $url = "http://www.example.jp/admin/index.php";

        $expected = "http://www.example.jp/admin/index.php";
        $actual = SC_Utils::getRealURL($url);

        $this->assertEquals($expected, $actual);
    }

    function testGetRealURL_変換有() {
        $url = "http://www.example.jp/admin/../index.php";

        $expected = "http://www.example.jp/index.php";
        $actual = SC_Utils::getRealURL($url);

        $this->assertEquals($expected, $actual);
    }

    function testGetRealURL_空のディレクトリ() {
        $url = "http://www.example.jp/admin/..///index.php";

        $expected = "http://www.example.jp/index.php";
        $actual = SC_Utils::getRealURL($url);

        $this->assertEquals($expected, $actual);
    }

    function testGetRealURL_Dotのディレクトリ() {
        $url = "http://www.example.jp/admin/././../index.php";

        $expected = "http://www.example.jp/index.php";
        $actual = SC_Utils::getRealURL($url);

        $this->assertEquals($expected, $actual);
    }
}
?>
