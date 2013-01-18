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

$HOME = realpath(dirname(__FILE__)) . "/../../..";
require_once($HOME . "/tests/class/Common_TestCase.php");

class SC_Session_checkUniqIdTest extends Common_TestCase {

    protected function setUp() {
        parent::setUp();
        $this->objSiteSession = new SC_SiteSession_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testCheckUniqId_POST値がない場合_True() {
        $_POST = null;
        $this->expected = true;
        $this->actual = $this->objSiteSession->checkUniqId();
        $this->verify('ポスト値空');
    }
    
    public function testCheckUniqId_POSTとセッションのUniqIDが一致する場合_True() {
        $_POST['uniqid'] = '1234567890';
        $_SESSION['site']['uniqid'] = '1234567890';
        
        $this->expected = true;
        $this->actual = $this->objSiteSession->checkUniqId();
        $this->verify('ユニークID一致');
    }
    
    public function testCheckUniqId_POSTとセッションのUniqIDが一致しない場合_False() {
        $_POST['uniqid'] = '0987654321';
        $_SESSION['site']['uniqid'] = '1234567890';
        
        $this->expected = false;
        $this->actual = $this->objSiteSession->checkUniqId();
        $this->verify('ユニークID不一致');
    }

}