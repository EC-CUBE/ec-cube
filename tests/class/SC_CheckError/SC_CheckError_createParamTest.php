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

class SC_CheckError_createParamTest extends Common_TestCase
{
    protected $old_reporting_level;
    protected $arrForm;
    protected $objErr;

    protected function setUp()
    {
        parent::setUp();
        $this->old_reporting_level = error_reporting();
        error_reporting($this->old_reporting_level ^ (E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE));
        $this->arrForm = array('form' => array(0=> 'A', 1 => "B", 2 => 'C'));
        $this->objErr = new SC_CheckError_Ex($this->arrForm);

    }

    protected function tearDown()
    {
        parent::tearDown();
        error_reporting($this->old_reporting_level);
    }

    /////////////////////////////////////////

    public function testArrParamIsCaracter()
    {
        $this->objErr->doFunc(array('EXIST_CHECK', "aabbcc_1234") ,array('EXIST_CHECK'));

        $this->expected = array('form' => array (0 => 'A',1 => 'B', 2 => 'C'),
                                'aabbcc_1234' => '');
        $this->actual = $this->objErr->arrParam;
        $this->verify('arrParam is normal character');
    }

    public function testArrParamIsIllegalCaracter()
    {
        $this->objErr->doFunc(array('EXIST_CHECK', "aabbcc_1234-") ,array('EXIST_CHECK'));

        $this->expected = array('form' => array (0 => 'A',1 => 'B', 2 => 'C'));
        $this->actual = $this->objErr->arrParam;
        $this->verify('arrParam is Illegal character');
    }


    public function testArrParamIsIllegalValue()
    {

        $this->arrForm = array('form' => '/../\\\.');
        $this->objErr = new SC_CheckError_Ex($this->arrForm);

        $this->objErr->doFunc(array('EXIST_CHECK', "form") ,array('EXIST_CHECK'));

        $this->expected = "※ EXIST_CHECKに禁止された記号の並びまたは制御文字が入っています。<br />";
        $this->actual = $this->objErr->arrErr['form'];
        $this->verify('arrParam is Illegal value');
    }

    public function testArrParamIsIllegalValue2()
    {
        $this->arrForm = array('form' => "\x00");
        $this->objErr = new SC_CheckError_Ex($this->arrForm);

        $this->objErr->doFunc(array('EXIST_CHECK', "form") ,array('EXIST_CHECK'));

        $this->expected = "※ EXIST_CHECKに禁止された記号の並びまたは制御文字が入っています。<br />";
        $this->actual = $this->objErr->arrErr['form'];
        $this->verify('arrParam is Illegal value2');
    }
}
