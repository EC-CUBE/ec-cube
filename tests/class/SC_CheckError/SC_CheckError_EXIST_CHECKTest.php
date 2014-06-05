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

class SC_CheckError_EXIST_CHECKTest extends Common_TestCase
{

    protected function setUp()
    {
        parent::setUp();
        $this->target_func = 'EXIST_CHECK';
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testEXIST_CHECK_formが空文字の場合_エラー()
    {
        $disp_name = $this->target_func;
        $arrForm = array('form' => '');
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array($disp_name, 'form'), array($this->target_func));

        $this->expected = "※ {$disp_name}が入力されていません。<br />";
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_formがnullの場合_エラー()
    {
        $disp_name = $this->target_func;
        $arrForm = array('form' => null);
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array($disp_name, 'form'), array($this->target_func));

        $this->expected = "※ {$disp_name}が入力されていません。<br />";
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_formがfalseの場合_エラー()
    {
        $disp_name = $this->target_func;
        $arrForm = array('form' => false);
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array($disp_name, 'form'), array($this->target_func));

        $this->expected = "※ {$disp_name}が入力されていません。<br />";
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_formがint0の場合_エラーではない()
    {
        $disp_name = $this->target_func;
        $arrForm = array('form' => 0);
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array($disp_name, 'form'), array($this->target_func));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_formがfloat0の場合_エラーではない()
    {
        $disp_name = $this->target_func;
        $arrForm = array('form' => 0.0);
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array($disp_name, 'form'), array($this->target_func));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_formがstring0の場合_エラーではない()
    {
        $disp_name = $this->target_func;
        $arrForm = array('form' => '0');
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array($disp_name, 'form'), array($this->target_func));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_formが普通の文字列の場合_エラーではない()
    {
        $disp_name = $this->target_func;
        $arrForm = array('form' => '普通のテスト文字列');
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array($disp_name, 'form'), array($this->target_func));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_formが空の配列の場合_エラー()
    {
        $disp_name = $this->target_func;
        $arrForm = array('form' => array());
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array($disp_name, 'form'), array($this->target_func));

        $this->expected = "※ {$disp_name}が選択されていません。<br />";
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_formが空文字の配列の場合_エラーではない()
    {
        $disp_name = $this->target_func;
        $arrForm = array('form' => array(''));
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array($disp_name, 'form'), array($this->target_func));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_formが0しか含まない配列の場合_エラーではない()
    {
        $disp_name = $this->target_func;
        $arrForm = array('form' => array(0));
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array($disp_name, 'form'), array($this->target_func));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_formが配列の場合_エラーではない()
    {
        $disp_name = $this->target_func;
        $arrForm = array('form' => array(1, 2, 3));
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array($disp_name, 'form'), array($this->target_func));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_formが連想配列の場合_エラーではない()
    {
        $disp_name = $this->target_func;
        $arrForm = array('form' => array(0=> 'A', 1 => 'B', 2 => 'C'));
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array($disp_name, 'form'), array($this->target_func));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }
}
