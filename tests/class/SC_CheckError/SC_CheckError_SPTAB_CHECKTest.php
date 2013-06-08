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

class SC_CheckError_SPTAB_CHECKTest extends Common_TestCase
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

    public function testSPTAB_CHECK_タブのみの入力()
    {
        $arrForm = array('form' => "\t");
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('SPTAB_CHECK', 'form') ,array('SPTAB_CHECK'));

        $this->expected = '※ SPTAB_CHECKにスペース、タブ、改行のみの入力はできません。<br />';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testSPTAB_CHECK_半角スペースのみの入力()
    {
        $arrForm = array('form' => " ");
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('SPTAB_CHECK', 'form') ,array('SPTAB_CHECK'));

        $this->expected = '※ SPTAB_CHECKにスペース、タブ、改行のみの入力はできません。<br />';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testSPTAB_CHECK_全角スペースのみの入力()
    {
        $arrForm = array('form' => "　");
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('SPTAB_CHECK', 'form') ,array('SPTAB_CHECK'));

        $this->expected = '※ SPTAB_CHECKにスペース、タブ、改行のみの入力はできません。<br />';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testSPTAB_CHECK_改行のみの入力()
    {
        $arrForm = array('form' => "\n");
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('SPTAB_CHECK', 'form') ,array('SPTAB_CHECK'));

        $this->expected = '※ SPTAB_CHECKにスペース、タブ、改行のみの入力はできません。<br />';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testSPTAB_CHECK_改行のみの入力2()
    {
        $arrForm = array('form' => "\r");
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('SPTAB_CHECK', 'form') ,array('SPTAB_CHECK'));

        $this->expected = '※ SPTAB_CHECKにスペース、タブ、改行のみの入力はできません。<br />';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testSPTAB_CHECK_スペース改行タブの混在()
    {
        $arrForm = array('form' => " 　\t\n\r");
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('SPTAB_CHECK', 'form') ,array('SPTAB_CHECK'));

        $this->expected = '※ SPTAB_CHECKにスペース、タブ、改行のみの入力はできません。<br />';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testSPTAB_CHECK_文字の先頭にスペース()
    {
        $arrForm = array('form' => " test");
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('SPTAB_CHECK', 'form') ,array('SPTAB_CHECK'));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testSPTAB_CHECK_文字の間にスペース()
    {
        $arrForm = array('form' => "te st");
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('SPTAB_CHECK', 'form') ,array('SPTAB_CHECK'));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testSPTAB_CHECK_文字の最後にスペース()
    {
        $arrForm = array('form' => "test ");
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('SPTAB_CHECK', 'form') ,array('SPTAB_CHECK'));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }
}
