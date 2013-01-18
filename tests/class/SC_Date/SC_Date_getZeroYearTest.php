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

class SC_Date_getZeroYearTest extends Common_TestCase {

    protected function setUp() {
        parent::setUp();
        $this->objDate = new SC_Date_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////
  
    public function testGetZeroYear_要素の数が4の配列を返す() {
        $this->expected = 4;
        $this->actual = count($this->objDate->getZeroYear());
        $this->verify("配列の長さ");
    }
    
    public function testGetZeroYear_最小値が2桁表記の今年の配列を返す() {
        $this->expected = DATE('y');
        $this->actual = min($this->objDate->getZeroYear());
        $this->verify("最小値；今年");
    }
    
    public function testGetZeroYear_最低値が引数の年の2桁表記の配列を返す() {
        $this->expected = '07';
        $this->actual = min($this->objDate->getZeroYear('2007'));

        $this->verify("引数が最低値");
    }
    
    public function testGetZeroYear_最低値がメンバー変数の年の2桁表記の配列を返す() {
        $this->expected = '04';
        $this->objDate->setStartYear('2004');
        $this->actual = min($this->objDate->getZeroYear());

        $this->verify("メンバー変数が最低値");
    }
    
    public function testGetZeroYear_最大値が3年後の2桁表記の配列を返す() {
        $this->expected = DATE('y')+3;
        $this->actual = max($this->objDate->getZeroYear());

        $this->verify("最大値；3年後");
    }
    
    public function testGetZeroYear_最大値がメンバ変数の2桁表記の配列を返す() {
        $this->expected = '20';
        $this->objDate->setEndYear('2020');
        $this->actual = max($this->objDate->getZeroYear());

        $this->verify("メンバー変数が最大値");
    }
}

