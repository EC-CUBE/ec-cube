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

require_once CLASS_EX_REALDIR . 'page_extends/frontparts/bloc/LC_Page_FrontParts_Bloc_Ex.php';

/**
 * Calendar のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $ $
 */
class LC_Page_FrontParts_Bloc_Calendar extends LC_Page_FrontParts_Bloc_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        // カレンダーデータ取得
        $this->arrCalendar = $this->lfGetCalendar(2);
    }

    /**
     * カレンダー情報取得.
     *
     * @param  integer $disp_month 表示する月数
     * @return array   カレンダー情報の配列を返す
     */
    public function lfGetCalendar($disp_month = 1)
    {
        $objDate = new SC_Date_Ex();
        $arrCalendar = array();
        $today = date('Y/m/d');

        for ($j = 0; $j <= $disp_month - 1; $j++) {
            $time = mktime(0, 0, 0, date('n') + $j, 1);
            $year = date('Y', $time);
            $month = date('n', $time);

            $objMonth = new Calendar_Month_Weekdays($year, $month, 0);
            $objMonth->build();
            $i = 0;
            while ($objDay = $objMonth->fetch()) {
                $arrCalendar[$j][$i]['in_month']    = $month == $objDay->month;
                $arrCalendar[$j][$i]['first']       = $objDay->first;
                $arrCalendar[$j][$i]['last']        = $objDay->last;
                $arrCalendar[$j][$i]['empty']       = $objDay->empty;
                $arrCalendar[$j][$i]['year']        = $year;
                $arrCalendar[$j][$i]['month']       = $month;
                $arrCalendar[$j][$i]['day']         = $objDay->day;
                $arrCalendar[$j][$i]['holiday']     = $objDate->isHoliday($year, $month, $objDay->day);
                $arrCalendar[$j][$i]['today']       = $today === sprintf('%04d/%02d/%02d', $year, $month, $objDay->day);

                $i++;
            }
        }

        return $arrCalendar;
    }
}
