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
        $firstDateOfNextMonth = Carbon::now()->addMonth(1)->startOfMonth();
        $endDateOfNextMonth = Carbon::now()->addMonth(1)->endOfMonth();

        // 2ヶ月間の定休日を取得
        $HolidaysOfTwoMonths = $this->calendarRepository->getHolidayList($firstDateOfThisMonth, $endDateOfNextMonth);

        // 今月のカレンダー配列を取得
        $thisMonthCalendar = $this->createCalendar($firstDateOfThisMonth);

        // 来月のカレンダー配列を取得
        $nextMonthCalendar = $this->createCalendar($firstDateOfNextMonth);

        // 定休日リストを取得
        $holidayListOfTwoMonths = [];
        foreach ($HolidaysOfTwoMonths as $Holiday) {
            $holidayListOfTwoMonths[] = $Holiday->getHoliday()->format('Ynj'); // 前ゼロなし年月日 例:202131
        }

        // 今月のカレンダー配列に定休日フラグを設定
        $thisMonthCalendar = $this->setHolidayFlag($thisMonthCalendar, $holidayListOfTwoMonths, Carbon::now());

        // 来月のカレンダー配列に定休日フラグを設定
        $nextMonthCalendar = $this->setHolidayFlag($nextMonthCalendar, $holidayListOfTwoMonths, Carbon::now()->addMonth(1));

        // 各カレンダータイトルを作成
        $thisMonthTitle = Carbon::now()->format('Y年n月');
        $nextMonthTitle = Carbon::now()->addMonth(1)->format('Y年n月');

        return [
            'ThisMonthTitle' => $thisMonthTitle,
            'NextMonthTitle' => $nextMonthTitle,
            'ThisMonthCalendar' => $thisMonthCalendar,
            'NextMonthCalendar' => $nextMonthCalendar,
            //'Holidays' => $HolidaysOfTwoMonths, TODO あとで消す
        ];
    }

    /**
     * カレンダー配列に定休日フラグを設定します
     *
     * @param array $targetMonthCalendar カレンダー配列
     * @param array $holidayListOfTwoMonths 定休日リスト
     * @param Carbon $targetDate ターゲット日
     *
     * @return array カレンダーの配列
     */
    private function setHolidayFlag($targetMonthCalendar, $holidayListOfTwoMonths, Carbon $targetDate)
    {
        for ($i = 0; $i < count($targetMonthCalendar); $i++) {
            // カレンダーの日付が定休日リストに存在するかを確認
            $result = array_search($targetDate->format('Yn').$targetMonthCalendar[$i]['day'], $holidayListOfTwoMonths);
            if ($result !== false) {
                $targetMonthCalendar[$i]['holiday'] = true;
            } else {
                $targetMonthCalendar[$i]['holiday'] = false;
            }
        }

        return $targetMonthCalendar;
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
