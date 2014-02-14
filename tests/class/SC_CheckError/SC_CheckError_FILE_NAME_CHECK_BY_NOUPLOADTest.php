<?php
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

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");

class SC_CheckError_FILE_NAME_CHECK_BY_NOUPLOADTest extends Common_TestCase
{

    public function setUp() {
        set_error_handler(function($errno, $errstr, $errfile, $errline) {
            throw new RuntimeException($errstr . " on line " . $errline . " in file " . $errfile);
        });
    }

    public function tearDown() {
        restore_error_handler();
    }

    public function validValueProvider()
    {
        return array(
            array('a'),
            array('012'),
            array('abc012'),
            array('a.txt'),
            array('a-b.zip'),
            array('a-b_c.tar.gz'),
        );
    }

    public function invalidValueProvider()
    {
        return array(
            array("line1\nline2"),
            array("a\x00b"),
            array('a/b'),
            array('a b'),
            array('日本語'),
            array('日 本 語'),
        );
    }

    public function testFILE_NAME_CHECK_BY_NOUPLOAD_空文字列の場合_エラーをセットしない()
    {
        $arrForm = array('file' => '');
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('label', 'file') ,array('FILE_NAME_CHECK_BY_NOUPLOAD'));

        $this->expected = false;
        $this->actual = isset($objErr->arrErr['file']);
        $this->verify();
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testFILE_NAME_CHECK_BY_NOUPLOAD_使用できない文字が含まれていない場合_エラーをセットしない($value)
    {
        $arrForm = array('file' => $value);
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('label', 'file') ,array('FILE_NAME_CHECK_BY_NOUPLOAD'));

        $this->expected = false;
        $this->actual = isset($objErr->arrErr['file']);
        $this->verify();
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testFILE_NAME_CHECK_BY_NOUPLOAD_使用できない文字が含まれている場合_エラーをセットする($value)
    {
        $arrForm = array('file' => $value);
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('label', 'file') ,array('FILE_NAME_CHECK_BY_NOUPLOAD'));

        $this->expected = true;
        $this->actual = isset($objErr->arrErr['file']);
        $this->verify();
    }

    /**
     * @depends testFILE_NAME_CHECK_BY_NOUPLOAD_使用できない文字が含まれている場合_エラーをセットする
     */
    public function testFILE_NAME_CHECK_BY_NOUPLOAD_他のエラーが既にセットされている場合_エラーを上書きしない()
    {
        $arrForm = array('file' => 'a/b');
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->arrErr['file'] = $other_error = 'Unknown error.';
        $objErr->doFunc(array('label', 'file') ,array('FILE_NAME_CHECK_BY_NOUPLOAD'));

        $this->expected = $other_error;
        $this->actual = $objErr->arrErr['file'];
        $this->verify();
    }
}
