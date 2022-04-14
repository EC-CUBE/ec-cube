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
     * @Route("/block/calendar", name="block_calendar", methods={"GET"})
     * @Template("Block/calendar.twig")
     */
    public function index(Request $request)
    {
        $today = Carbon::now();
        $firstDateOfThisMonth = $today->copy()->startOfMonth();
        $firstDateOfNextMonth = $today->copy()->startOfMonth()->addMonth(1)->startOfMonth();
        $endDateOfNextMonth = $today->copy()->startOfMonth()->addMonth(1)->endOfMonth();

        // 2ヶ月間の定休日を取得
        $HolidaysOfTwoMonths = $this->calendarRepository->getHolidayList($firstDateOfThisMonth, $endDateOfNextMonth);

        // 今月のカレンダー配列を取得
        $thisMonthCalendar = $this->createCalendar($firstDateOfThisMonth);

        // 来月のカレンダー配列を取得
        $nextMonthCalendar = $this->createCalendar($firstDateOfNextMonth);

        // 定休日リストを取得
        $holidayListOfTwoMonths = [];
        foreach ($HolidaysOfTwoMonths as $Holiday) {
            $holidayListOfTwoMonths[] = $Holiday->getHoliday();
        }

        // 今月のカレンダー配列に定休日フラグを設定
        $thisMonthCalendar = $this->setHolidayAndTodayFlag($thisMonthCalendar, $holidayListOfTwoMonths, $today->copy());

        // 来月のカレンダー配列に定休日フラグを設定
        $nextMonthCalendar = $this->setHolidayAndTodayFlag($nextMonthCalendar, $holidayListOfTwoMonths, $today->copy()->startOfMonth()->addMonth(1));

        // 各カレンダータイトルを作成
        $monthFormat = $this->translator->trans('front.block.calendar.month_format');
        $thisMonthTitle = $firstDateOfThisMonth->format($monthFormat);
        $nextMonthTitle = $firstDateOfNextMonth->format($monthFormat);

        return [
            'ThisMonthTitle' => $thisMonthTitle,
            'NextMonthTitle' => $nextMonthTitle,
            'ThisMonthCalendar' => $thisMonthCalendar,
            'NextMonthCalendar' => $nextMonthCalendar,
        ];
    }

    /**
     * カレンダー配列に定休日と今日フラグを設定します
     *
     * @param array $targetMonthCalendar カレンダー配列
     * @param array $holidayListOfTwoMonths 定休日リスト
     * @param Carbon $targetDate ターゲット日
     *
     * @return array カレンダーの配列
     */
    private function setHolidayAndTodayFlag($targetMonthCalendar, $holidayListOfTwoMonths, Carbon $targetDate)
    {
        for ($i = 0; $i < count($targetMonthCalendar); $i++) {
            // カレンダー配列の日が空の場合は処理をスキップ
            if ($targetMonthCalendar[$i]['day'] == '') {
                $targetMonthCalendar[$i]['holiday'] = false;
                $targetMonthCalendar[$i]['today'] = false;
                continue;
            }

            $targetYmdDateTime = new \DateTime($targetDate->copy()->format('Y-n').'-'.$targetMonthCalendar[$i]['day']);

            // カレンダーの日付が定休日リストに存在するかを確認
            $result = array_search($targetYmdDateTime, $holidayListOfTwoMonths);
            // 定休日フラグを設定
            if ($result !== false) {
                $targetMonthCalendar[$i]['holiday'] = true;
            } else {
                $targetMonthCalendar[$i]['holiday'] = false;
            }

            // 今日フラグを設定
            if ($targetYmdDateTime == new \DateTime($targetDate->copy()->format('Y-n-j'))) {
                $targetMonthCalendar[$i]['today'] = true;
            } else {
                $targetMonthCalendar[$i]['today'] = false;
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
            $targetMonthCalendar[$i]['day'] = '';
            $targetMonthCalendar[$i]['dayOfWeek'] = '';
        }

        // 1日目の曜日の位置＋月の日数
        $loopCount = $firstDayOfWeek + $firstDateOfTargetMonth->daysInMonth;

        // 月の日数に合わせて日と曜日を追加
        $dayNumber = 1;
        $dayOfWeekNumber = $firstDayOfWeek;
        for ($i = $firstDayOfWeek; $i < $loopCount; $i++) {
            $targetMonthCalendar[$i]['day'] = $dayNumber;
            $targetMonthCalendar[$i]['dayOfWeek'] = $this->getDayOfWeekString($dayOfWeekNumber);
            $dayNumber++;
            $dayOfWeekNumber++;

            // 曜日のおりかえし： 0 (日曜)へ
            if ($dayOfWeekNumber == 7) {
                $dayOfWeekNumber = 0;
            }
        }

        // 1日目の曜日の位置＋月の日数に合わせて後に空文字を追加
        // 7日*4週=28日(日曜始まりでうるう年じゃない2月)
        if ($loopCount === 28) {
            // 後に空文字追加はスキップ
            return $targetMonthCalendar;
        }
        // 7日*6週=42日、7日*5週=35日
        $paddingLoopCount = 35;
        if ($loopCount > 35) {
            $paddingLoopCount = 42;
        }
        for ($i = $loopCount; $i < $paddingLoopCount; $i++) {
            $targetMonthCalendar[$i]['day'] = '';
            $targetMonthCalendar[$i]['dayOfWeek'] = '';
        }

        return $targetMonthCalendar;
    }

    /**
     * 曜日を数値から文字列へ変換します
     *
     * @param int $dayOfWeekNumber 曜日の番号 : 0 (日曜)から 6 (土曜)
     *
     * @return string 曜日の文字 : Sun(日曜)からSat(土曜)
     */
    private function getDayOfWeekString($dayOfWeekNumber)
    {
        $weekday = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        return $weekday[$dayOfWeekNumber];
    }
}
