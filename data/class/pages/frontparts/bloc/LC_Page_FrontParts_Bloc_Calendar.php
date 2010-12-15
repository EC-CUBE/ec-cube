<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
$current_dir = realpath(dirname(__FILE__));
define('CALENDAR_ROOT', DATA_PATH.'module/Calendar'.DIRECTORY_SEPARATOR);
require_once($current_dir . "/../../../../module/Calendar/Month/Weekdays.php");
require_once(CLASS_PATH . "pages/frontparts/bloc/LC_Page_FrontParts_Bloc.php");

/**
 * Calendar のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $ $
 */
class LC_Page_FrontParts_Bloc_Calendar extends LC_Page_FrontParts_Bloc {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->setTplMainpage('calendar.tpl');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        // 休日取得取得
        $this->arrHoliday = $this->lfGetHoliday();
        // 定休日取得取得
        $this->arrRegularHoliday = $this->lfGetRegularHoliday();
        // カレンダーデータ取得
        $this->arrCalendar = $this->lfGetCalendar(2);
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
         $this->tpl_mainpage = MOBILE_TEMPLATE_DIR . "frontparts/"
            . BLOC_DIR . 'best5.tpl';
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        $this->process();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // カレンダー情報取得
    function lfGetCalendar($disp_month = 1){

        for ($j = 0; $j <= $disp_month-1; ++$j) {
            $year = date('Y');
            $month = date('n') + $j;
            if ($month > 12) {
                $month = $month%12;
                $year = $year + $month%12;
            }

            $Month = new Calendar_Month_Weekdays($year, $month, 0);
            $Month->build();
            $i = 0;
            while ($Day = $Month->fetch()) {
                if ($month == $Day->month) {
                    $arrCalendar[$j][$i]['in_month'] = true;
                } else {
                    $arrCalendar[$j][$i]['in_month'] = false;
                }
                $arrCalendar[$j][$i]['first'] = $Day->first;
                $arrCalendar[$j][$i]['last'] = $Day->last;
                $arrCalendar[$j][$i]['empty'] = $Day->empty;
                $arrCalendar[$j][$i]['year'] = $year;
                $arrCalendar[$j][$i]['month'] = $month;
                $arrCalendar[$j][$i]['day'] = $Day->day;
                if ($this->lfCheckHoliday($year, $month, $Day->day)) {
                    $arrCalendar[$j][$i]['holiday'] = true;
                } else {
                    $arrCalendar[$j][$i]['holiday'] = false;
                }
                ++$i;
            }
        }

        return $arrCalendar;
    }

    // 休日取得
    function lfGetHoliday() {
        $objQuery = new SC_Query();
        $objQuery->setOrder("rank DESC");

        $where = "del_flg <> 1";
        $arrRet = $objQuery->select("month, day", "dtb_holiday", $where);
        foreach ($arrRet AS $key=>$val) {
            $arrHoliday[$val['month']][] = $val['day'];
        }
        return $arrHoliday;
    }

    // 定休日取得
    function lfGetRegularHoliday() {
        $objSIteInfo = new SC_SiteInfo();
        $arrRegularHoliday = explode('|', $objSIteInfo->data['regular_holiday_ids']);
        return $arrRegularHoliday;
    }

    // 休日チェック
    function lfCheckHoliday($year, $month, $day) {
        if (!empty($this->arrHoliday[$month])) {
            if (in_array($day, $this->arrHoliday[$month])) {
                return true;
            }
        }
        if (!empty($this->arrRegularHoliday)) {
            $w = date('w', mktime(0,0,0 ,$month, $day, $year));
            if (in_array($w, $this->arrRegularHoliday)) {
                return true;
            }
        }
        return false;
    }

}
?>
