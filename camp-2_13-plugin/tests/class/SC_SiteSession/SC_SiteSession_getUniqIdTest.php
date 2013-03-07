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

class SC_Session_getUniqIdTest extends Common_TestCase
{

    protected function setUp()
    {
        parent::setUp();
        $this->objSiteSession = new SC_SiteSession_Mock();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testGetUniqId_設定済みのユニークなID取得する()
    {
        $_SESSION['site']['uniqid'] = '0987654321';
        $this->expected = '0987654321';
        $this->actual = $this->objSiteSession->getUniqId();
        $this->verify('ユニークID');
    }
    
    public function testGetUniqId_新たにユニークなID取得する()
    {
        $_SESSION['site']['uniqid'] = '';
        $this->expected = '1234567890';
        $this->actual = $this->objSiteSession->getUniqId();
        $this->verify('ユニークID');
    }
}

class SC_SiteSession_Mock extends SC_SiteSession_Ex
{
    function setUniqId()
    {
        $_SESSION['site']['uniqid'] = '1234567890';
    }
}