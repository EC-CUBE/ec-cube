<?php
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

        $expected = "http://www.example.jp:/admin/index.php";
        $actual = SC_Utils::getRealURL($url);

        $this->assertEquals($expected, $actual);
    }

    function testGetRealURL_変換有() {
        $url = "http://www.example.jp/admin/../index.php";

        $expected = "http://www.example.jp:/index.php";
        $actual = SC_Utils::getRealURL($url);

        $this->assertEquals($expected, $actual);
    }

    function testGetRealURL_空のディレクトリ() {
        $url = "http://www.example.jp/admin/..///index.php";

        $expected = "http://www.example.jp:/index.php";
        $actual = SC_Utils::getRealURL($url);

        $this->assertEquals($expected, $actual);
    }

    function testGetRealURL_Dotのディレクトリ() {
        $url = "http://www.example.jp/admin/././../index.php";

        $expected = "http://www.example.jp:/index.php";
        $actual = SC_Utils::getRealURL($url);

        $this->assertEquals($expected, $actual);
    }

    function testIsBlank() {
        $val = "";
        $this->assertTrue(SC_Utils::isBlank($val));

        $valIsNotBlank = "\x00..\x1F  a \n\t";
        $this->assertTrue(SC_Utils::isBlank($val));

        $wideSpace = "　";
        $this->assertTrue(SC_Utils::isBlank($wideSpace));
        // greedy is false
        $this->assertFalse(SC_Utils::isBlank($wideSpace, false));

        $array = array();
        $this->assertTrue(SC_Utils::isBlank($array));

        $nestsArray = array(array(array()));
        $this->assertTrue(SC_Utils::isBlank($nestsArray));
        // greedy is false
        $this->assertFalse(SC_Utils::isBlank($nestsArray, false));

        $nestsArrayIsNotBlank = array(array(array('1')));
        $this->assertFalse(SC_Utils::isBlank($nestsArrayIsNotBlank));
        // greedy is false
        $this->assertFalse(SC_Utils::isBlank($nestsArrayIsNotBlank, false));

        $wideSpaceAndBlank = array(array("　\n　"));
        $this->assertTrue(SC_Utils::isBlank($wideSpaceAndBlank));
        // greedy is false
        $this->assertFalse(SC_Utils::isBlank($wideSpaceAndBlank, false));

        $wideSpaceIsNotBlank = array(array("　\na　"));
        $this->assertFalse(SC_Utils::isBlank($wideSpaceIsNotBlank));
        // greedy is false
        $this->assertFalse(SC_Utils::isBlank($wideSpaceIsNotBlank, false));

        $zero = 0;
        $this->assertFalse(SC_Utils::isBlank($zero));
        $this->assertFalse(SC_Utils::isBlank($zero, false));

        $emptyArray[0] = "";
        $this->assertTrue(SC_Utils::isBlank($emptyArray));
    }

    function testIsAbsoluteRealPath() {
        // for *NIX
        if (strpos(PHP_OS, 'WIN') === false) {
            $unix_absolute = '/usr/local';
            $this->assertTrue(SC_Utils::isAbsoluteRealPath($unix_absolute));

            $relative = '../foo/bar';
            $this->assertFalse(SC_Utils::isAbsoluteRealPath($relative));
        }
        // for Win
        else {
            $win_absolute = 'C:\Windows\system32';
            $this->assertTrue(SC_Utils::isAbsoluteRealPath($win_absolute));

            $win_absolute = 'C:/Windows/system32';
            $this->assertTrue(SC_Utils::isAbsoluteRealPath($win_absolute));

            $relative = '..\\foo\\bar';
            $this->assertFalse(SC_Utils::isAbsoluteRealPath($relative));

        }

        $empty = '';
        $this->assertFalse(SC_Utils::isAbsoluteRealPath($empty));
    }

    function testRecursiveMkdir() {
        $tmp_dir = sys_get_temp_dir();
        $dir = '/foo/bar';
        $results = false;
        if (is_dir($tmp_dir . $dir)) {
            rmdir($tmp_dir . '/foo/bar');
            rmdir($tmp_dir . '/foo');
        }
        $results = SC_Utils::recursiveMkdir($tmp_dir . $dir, 0777);
        $this->assertTrue($results);
    }
}
?>
