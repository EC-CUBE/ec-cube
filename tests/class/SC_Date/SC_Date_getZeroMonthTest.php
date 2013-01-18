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

class SC_Date_getZeroMonthTest extends Common_TestCase {

    protected function setUp() {
        parent::setUp();
        $this->objDate = new SC_Date_Ex();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testGetZeroMonth_要素の数が12の配列を返す() {
        $this->expected = 12;
        $this->actual = count($this->objDate->getZeroMonth());

        $this->verify("配列の長さ");
    }

    public function testGetZeroMonth_0をつけた月の配列を返す() {
        $this->expected = array('01'=>'01','02'=>'02','03'=>'03'
                                ,'04'=>'04','05'=>'05','06'=>'06'
                                ,'07'=>'07','08'=>'08','09'=>'09'
                                ,'10'=>'10','11'=>'11','12'=>'12'
                                );
        $this->actual = $this->objDate->getZeroMonth();

        $this->verify("配列の最低値");
    }

}
