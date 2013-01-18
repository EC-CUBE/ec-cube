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

class SC_Date_getDayTest extends Common_TestCase {

    protected function setUp() {
        parent::setUp();
        $this->objDate = new SC_Date_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testGetDay_要素の数が31の配列を返す() {
        $this->expected = 31;
        $this->actual = count($this->objDate->getDay());

        $this->verify("配列の長さ");
    }

    public function testGetDay_要素の最低値が1の配列を返す() {
        $this->expected = 1;
        $this->actual = min($this->objDate->getDay());

        $this->verify("配列の最低値");
    }

    public function testGetDay_要素の最大値が31の配列を返す() {
        $this->expected = 31;
        $this->actual = max($this->objDate->getDay());

        $this->verify("配列の最大値");
    }

    public function testGetDay_TRUEを与えた場合要素の数が32の配列を返す() {
        $this->expected = 32;
        $this->actual = count($this->objDate->getDay(true));

        $this->verify("デフォルトを設定した配列の長さ");
    }

    public function testGetDay_TRUEを与えた場合ーー含まれるの配列を返す() {
        $result = in_array('--', $this->objDate->getDay(true));

        $this->assertTrue($result, "デフォルトの値");
    }
 
}

