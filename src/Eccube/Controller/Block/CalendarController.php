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

use Carbon\Carbon;
use Eccube\Controller\AbstractController;
use Eccube\Repository\CalendarRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    /**
     * @var CalendarRepository
     */
    protected $calendarRepository;

    /**
     * CalendarController constructor.
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
        $firstDateOfThisMonth = Carbon::now()->startOfMonth();
        $firstDateOfNextMonth = Carbon::parse('+ 1 month')->startOfMonth();
        $endDateOfNextMonth = Carbon::parse('+ 1 month')->endOfMonth();

        // 2ヶ月間の定休日を取得
        $Holidays = $this->calendarRepository->getHolidayList($firstDateOfThisMonth, $endDateOfNextMonth);

        // 今月のカレンダー配列を取得
        $thisMonthCalendar = $this->createCalendar($firstDateOfThisMonth);

        // 来月のカレンダー配列を取得
        $nextMonthCalendar = $this->createCalendar($firstDateOfNextMonth);

        // TODO あとやりたいことは休日データ取ってフラグ入れる？
        $holidayList = [];
        foreach ($Holidays as $Holiday) {
            $holidayList[] = $Holiday->getHoliday()->format('nj'); // 月日 例:3月1日->31
        }

        //$result = array_search(Carbon::create(2021, 3, 10, 15, 0, 0), $holidayList);
        $result = array_search('311', $holidayList);

        //$result = $Holidays[array_search(Carbon::create(2021, 2, 11), $holidayList)];
        if ($result !== false) {
            $result = 'あったよ！';
        } else {
            $result = 'ザンネン！';
        }

        for ($i = 0; $i <= count($thisMonthCalendar); $i++) {
//            if (in_array($thisMonthCalendar[$i]['day'], $holidays)) {
//                $thisMonthCalendar[$i]['holiday'] = true;
//            }
        }

        return [
            'ThisMonthCalendar' => $thisMonthCalendar,
            'NextMonthCalendar' => $nextMonthCalendar,
            'Holidays' => $Holidays,
            'Temp' => $result,
        ];
    }

    /**
     * カレンダーの配列を生成します
     *
     * @param Carbon $firstDateOfTargetMonth 月初日
     *
     * @return array カレンダーの配列
     */
    private function createCalendar(Carbon $firstDateOfTargetMonth)
    {
        // 週のうちの何日目か 0 (日曜)から 6 (土曜)を取得
        $firstDayOfWeek = $firstDateOfTargetMonth->dayOfWeek;

        $targetMonthCalendar = [];

        // 1日目の曜日の位置手前まで空文字を追加
        for ($i = 0; $i <= $firstDayOfWeek; $i++) {
            $targetMonthCalendar[$i]['day'] = '@'; // TODO あとで空文字に変えよう
        }

        // 1日目の曜日の位置＋月の日数
        $loopCount = $firstDayOfWeek + $firstDateOfTargetMonth->daysInMonth;

        // 月の日数に合わせて日を追加
        $dayNumber = 1;
        for ($i = $firstDayOfWeek; $i < $loopCount; $i++) {
            $targetMonthCalendar[$i]['day'] = $dayNumber;
            $dayNumber++;
        }

        // 1日目の曜日の位置＋月の日数に合わせて後に空文字を追加
        // 7日*6週=42日、7日*5週=35日
        $paddingLoopCount = 35;
        if ($loopCount > 35) {
            $paddingLoopCount = 42;
        }
        for ($i = $loopCount; $i < $paddingLoopCount; $i++) {
            $targetMonthCalendar[$i]['day'] = '@'; // TODO あとで空文字に変えよう
        }

        return $targetMonthCalendar;
    }
}
