<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Page\Bloc;

use Eccube\Application;
use Eccube\Framework\Date;

/**
 * Calendar のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class Calendar extends AbstractBloc
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
        /* @var $objDate Date */
        $objDate = Application::alias('eccube.date');
        $arrCalendar = array();
        $today = date('Y/m/d');

        for ($j = 0; $j <= $disp_month - 1; $j++) {
            $time = mktime(0, 0, 0, date('n') + $j, 1);
            $year = date('Y', $time);
            $month = date('n', $time);

            $objMonth = new \Calendar_Month_Weekdays($year, $month, 0);
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
