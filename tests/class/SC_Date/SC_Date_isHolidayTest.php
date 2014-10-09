<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

class SC_Date_isHolidayTest extends Common_TestCase
{

    protected function setUp()
    {
        parent::setUp();
        $this->objDate = new SC_Date_Ex();
        $objQuery = SC_Query_Ex::getSingletonInstance();
        //休日を登録
        $holiday = array(
            array(
                'holiday_id' => '1',
                'title' => 'TEST HOLIDAY1',
                'month' => '2',
                'day' => '14',
                'rank' => '1',
                'creator_id' => '1',
                'create_date' => '2013-02-14 11:22:33',
                'update_date' => '2013-02-14 22:11:33',
                'del_flg' => '0'                
                  ),
            array(
                'holiday_id' => '2',
                'title' => 'TEST HOLIDAY2',
                'month' => '3',
                'day' => '26',
                'rank' => '2',
                'creator_id' => '1',
                'create_date' => '2013-02-15 11:22:33',
                'update_date' => '2013-02-16 22:11:33',
                'del_flg' => '0'                
                  )
            );
        //休みの曜日を登録
        $baseInfo = array(
            'id' => '1',
            'regular_holiday_ids' => '0|6', // 土日を休みに登録
            'update_date' => '2013-02-14 22:11:33'
        );

        $objQuery->delete('dtb_holiday');
        $objQuery->delete('dtb_baseinfo');
        foreach ($holiday as $key => $item) {
            $objQuery->insert('dtb_holiday', $item);
        }
        $objQuery->insert('dtb_baseinfo', $baseInfo);
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /////////////////////////////////////////

    public function testIsHoliday_日付が登録されている休日の場合_TRUEを返す()
    {

        $this->expected = true;
        $year = 2013;
        $month = 2;
        $day = 14;
        $this->actual = $this->objDate->isHoliday($year, $month, $day);

        $this->verify("登録された休日");
    }
    
    public function testIsHoliday_日付が休日ではない場合_FALSEを返す()
    {

        $this->expected = false;
        $year = 2013;
        $month = 1;
        $day = 23;
        $this->actual = $this->objDate->isHoliday($year, $month, $day);

        $this->verify("休日ではない");
    }

    public function testIsHoliday_休みの曜日の場合_trueを返す()
    {

        $this->expected = true;
        $year = 2013;
        $month = 3;
        $day = 10;
        $this->actual = $this->objDate->isHoliday($year, $month, $day);

        $this->verify("休みの曜日");
    }
      
    public function testIsHoliday_休みの曜日でない場合_falseを返す()
    {

        $this->expected = false;
        $year = 2013;
        $month = 3;
        $day = 11;
        $this->actual = $this->objDate->isHoliday($year, $month, $day);

        $this->verify("休みの曜日");
    } 
    
}

