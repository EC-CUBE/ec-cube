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

class SC_Session_isPrepageTest extends Common_TestCase {

    protected function setUp() {
        parent::setUp();
        $this->objSiteSession = new SC_SiteSession_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testIsPrepage_sessionが空の場合_false() {
        $this->expected = false;
        $this->actual = $this->objSiteSession->isPrepage();
        $this->verify("ページ判定");
    }
    
    public function testIsPrepage_prepageとnowpageが違う場合_false() {
        $this->expected = false;
        $_SESSION['site']['pre_page'] = 'test.php';
        $this->actual = $this->objSiteSession->isPrepage();
        $this->verify("ページ判定");
    }
    
    public function testIsPrepage_prepageとnowpageが同じの場合_true() {
        $this->expected = true;
        $_SESSION['site']['pre_page'] = $_SERVER['SCRIPT_NAME'];
        $this->actual = $this->objSiteSession->isPrepage();
        $this->verify("ページ判定");
    }
    
    public function testIsPrepage_pre_regist_successがtrueの場合_true() {
        $this->expected = true;
        $_SESSION['site']['pre_page'] = 'test.php';
        $_SESSION['site']['pre_regist_success'] = true;
        $this->actual = $this->objSiteSession->isPrepage();
        $this->verify("ページ判定");
    }
}

