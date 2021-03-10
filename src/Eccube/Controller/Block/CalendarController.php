<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Block;

use Eccube\Controller\AbstractController;
use Eccube\Repository\CalendarRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Carbon\Carbon;

class CalendarController extends AbstractController
{
    /**
     * @var CalendarRepository
     */
    protected $calendarRepository;

    /**
     * CalendarController constructor.
     *
     *  @param CalendarRepository $calendarRepository
     */
    public function __construct(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    /**
     * @Route("/block/calendar", name="block_calendar")
     * @Template("Block/calendar.twig")
     */
    public function index(Request $request)
    {
        // TODO あとやりたいことは月初の前にどんだけ空白埋めるか？と休日データ取ってフラグ入れる？

        // 当月と翌月で指定して定休日データ取る？
        $Holidays = $this->calendarRepository->getHoridayListOfLastTwoMonths();

        // 今月のカレンダーを作る
        $today = Carbon::now();
        $thisMonthFirstDayOfWeek = $today->startOfMonth()->dayOfWeek; // 月初の曜日
        $thisMonthCalendar = [];
        for ($i = 1; $i <= $today->daysInMonth; $i++) {
            $thisMonthCalendar[$i]['day'] = $i;
            $thisMonthCalendar[$i]['dayOfWeek'] = $thisMonthFirstDayOfWeek; // ホントは曜日詰めなくていい確認だけ
            if ($thisMonthFirstDayOfWeek == 6) {
                $thisMonthFirstDayOfWeek = 0; // 曜日を日曜に戻す
            } else {
                $thisMonthFirstDayOfWeek++;
            }
        }

        // 来月のカレンダーを作る
        $nextMonth = Carbon::parse('+ 1 month');
        $nextMonthFirstDayOfWeek = $nextMonth->startOfMonth()->dayOfWeek; // 月初の曜日
        $nextMonthCalendar = [];
        for ($i = 1; $i <= $nextMonth->daysInMonth; $i++) {
            $nextMonthCalendar[$i]['day'] = $i;
            $nextMonthCalendar[$i]['dayOfWeek'] = $nextMonthFirstDayOfWeek; // ホントは曜日詰めなくていい確認だけ
            if ($nextMonthFirstDayOfWeek == 6) {
                $nextMonthFirstDayOfWeek = 0; // 曜日を日曜に戻す
            } else {
                $nextMonthFirstDayOfWeek++;
            }
        }

        return [
            'ThisMonthCalendar' => $thisMonthCalendar,
            'NextMonthCalendar' => $nextMonthCalendar,
            'Holidays' => $Holidays,
        ];
    }
}
