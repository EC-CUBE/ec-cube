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

class SC_Date_AccessorTest extends Common_TestCase {

    protected function setUp() {
        parent::setUp();
        $this->objDate = new SC_Date_Ex('2010','2014');
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testGetStartYear_startYearの値を取得する() {
        $this->expected = '2010';
        $this->actual = $this->objDate->getStartYear();

        $this->verify("StartYear");
    }

    public function testGetEndYear_endYearの値を取得する() {
        $this->expected = '2014';
        $this->actual = $this->objDate->getEndYear();

        $this->verify("EndYear");
    }

    public function testsetMonth_monthの値を設定する() {
        $this->expected = '9';
        $this->objDate->setMonth('9');
        $this->actual = $this->objDate->month;

        $this->verify("Month");
    }
    
    public function testsetDay_dayの値を設定する() {
        $this->expected = '28';
        $this->objDate->setDay('28');
        $this->actual = $this->objDate->day;

        $this->verify("Day");
    }
}

