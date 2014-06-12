<?php
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

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");

class SC_CheckError_EXIST_CHECK_REVERSETest extends Common_TestCase
{

    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testEXIST_CHECK_REVERSE_formが空()
    {
        $arrForm = array('form' => '');
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('form', 'EXIST_CHECK_REVERSE') ,array('EXIST_CHECK_REVERSE'));

        $this->expected = '※ EXIST_CHECK_REVERSEが入力されていません。<br />';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_REVERSE_formがNULL()
    {
        $arrForm = array('form' => NULL);
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('form', 'EXIST_CHECK_REVERSE') ,array('EXIST_CHECK_REVERSE'));

        $this->expected = '※ EXIST_CHECK_REVERSEが入力されていません。<br />';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_REVERSE_formがint0()
    {
        $arrForm = array('form' => 0);
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('form', 'EXIST_CHECK_REVERSE') ,array('EXIST_CHECK_REVERSE'));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testEXIST_CHECK_REVERSE_formがstring0()
    {
        $arrForm = array('form' => '0');
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('form', 'EXIST_CHECK_REVERSE') ,array('EXIST_CHECK_REVERSE'));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }
}
