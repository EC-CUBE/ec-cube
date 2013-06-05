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

class SC_CheckError_HTML_TAG_CHECKTest extends Common_TestCase
{

    protected function setUp()
    {
        parent::setUp();
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrAllowedTag = $masterData->getMasterData('mtb_allowed_tag');
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testHTML_TAG_CHECK_scriptタグが含まれる()
    {
        $arrForm = array('form' => '<script></script>');
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('HTML_TAG_CHECK', 'form', $this->arrAllowedTag) ,array('HTML_TAG_CHECK'));

        $this->expected = '※ HTML_TAG_CHECKに許可されていないタグ [script], [script] が含まれています。<br />';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testHTML_TAG_CHECK_pタグが含まれる()
    {
        $arrForm = array('form' => '<p><p><p>');
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('HTML_TAG_CHECK', 'form', $this->arrAllowedTag) ,array('HTML_TAG_CHECK'));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

    public function testHTML_TAG_CHECK_htmlタグが含まれない()
    {
        $arrForm = array('form' => '
            htmlを含まないテスト文章。
            htmlを含まないテスト文章。
            htmlを含まないテスト文章。
            ');
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->doFunc(array('HTML_TAG_CHECK', 'form', $this->arrAllowedTag) ,array('HTML_TAG_CHECK'));

        $this->expected = '';
        $this->actual = $objErr->arrErr['form'];
        $this->verify('');
    }

}
