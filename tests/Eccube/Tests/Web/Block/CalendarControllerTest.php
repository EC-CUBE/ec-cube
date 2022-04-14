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

namespace Eccube\Tests\Web\Block;

use Carbon\Carbon;
use Eccube\Entity\Calendar;
use Eccube\Tests\Web\AbstractWebTestCase;

class CalendarControllerTest extends AbstractWebTestCase
{
    public function testRoutingCalendar()
    {
        $this->client->request('GET', '/block/calendar');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testThisMonthTitle()
    {
        $crawler = $this->client->request('GET', $this->generateUrl('block_calendar'));
        $this->expected = Carbon::now()->startOfMonth()->format('Y年n月');
        $this->actual = $crawler->filter('#this-month-title')->text();
        $this->verify();
    }

    public function testNextMonthTitle()
    {
        $crawler = $this->client->request('GET', $this->generateUrl('block_calendar'));
        $this->expected = Carbon::now()->startOfMonth()->addMonth(1)->format('Y年n月');
        $this->actual = $crawler->filter('#next-month-title')->text();
        $this->verify();
    }

    public function testTodayAndHolidayStyle()
    {
        $Calendar = new Calendar();
        $Calendar->setTitle('今日かつ定休日のパターン')
            ->setHoliday(new \DateTime(Carbon::now()->format('Y-m-d')));
        $this->entityManager->persist($Calendar);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', $this->generateUrl('block_calendar'));
        $this->expected = Carbon::now()->format('j');
        $this->actual = $crawler->filter('#today-and-holiday')->text();
        $this->verify();
    }

    public function testHolidayStyle()
    {
        // 土日以外の日を取得
        $targetHoliday = Carbon::now()->addDay(1);

        if ($targetHoliday->isSaturday()) {
            if (!$targetHoliday->copy()->addDay(2)->isCurrentMonth()) {
                $targetHoliday = $targetHoliday->addDay(-1);
            } else {
                $targetHoliday = $targetHoliday->addDay(2);
            }
        } elseif ($targetHoliday->isSunday()) {
            if (!$targetHoliday->copy()->addDay(1)->isCurrentMonth()) {
                $targetHoliday = $targetHoliday->addDay(-2);
            } else {
                $targetHoliday = $targetHoliday->addDay(1);
            }
        }

        $Calendar = new Calendar();
        $Calendar->setTitle('今日ではない定休日のパターン')
            ->setHoliday(new \DateTime($targetHoliday->format('Y-m-d')));
        $this->entityManager->persist($Calendar);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', $this->generateUrl('block_calendar'));
        $this->expected = $targetHoliday->format('j');
        $this->actual = $crawler->filter(($targetHoliday->isCurrentMonth() ? '#this-month-holiday-' : '#next-month-holiday-').$this->expected)->text();
        $this->verify();
    }

    public function testWeekendHolidaysStyle()
    {
        // 月初日を取得
        $firstDayOfThisMonth = Carbon::now()->firstOfMonth();

        // 月初の日曜日を取得
        $sunday = null;
        $sundayDayOfWeekNumber = $firstDayOfThisMonth->dayOfWeek;
        if ($sundayDayOfWeekNumber == 0) { // Sun
            $sunday = $firstDayOfThisMonth->copy();
        } elseif ($sundayDayOfWeekNumber == 1) { // Mon
            $sunday = $firstDayOfThisMonth->copy()->addDay(6);
        } elseif ($sundayDayOfWeekNumber == 2) { // Tue
            $sunday = $firstDayOfThisMonth->copy()->addDay(5);
        } elseif ($sundayDayOfWeekNumber == 3) { // Wed
            $sunday = $firstDayOfThisMonth->copy()->addDay(4);
        } elseif ($sundayDayOfWeekNumber == 4) { // Thu
            $sunday = $firstDayOfThisMonth->copy()->addDay(3);
        } elseif ($sundayDayOfWeekNumber == 5) { // Fri
            $sunday = $firstDayOfThisMonth->copy()->addDay(2);
        } elseif ($sundayDayOfWeekNumber == 6) { // Sat
            $sunday = $firstDayOfThisMonth->copy()->addDay(1);
        }
        // 日曜の前日が今月かどうかで月初の土曜日を取得
        $saturday = null;
        if ($sunday->copy()->addDays(-1)->isCurrentMonth()) {
            $saturday = $sunday->copy()->addDays(-1);
        } else {
            $saturday = $sunday->copy()->addDays(6);
        }

        $crawler = $this->client->request('GET', $this->generateUrl('block_calendar'));

        // 土曜日の確認
        $this->expected = $saturday->format('j');
        $this->actual = $crawler->filter('#this-month-holiday-'.$this->expected)->text();
        $this->verify();

        // 日曜日の確認
        $this->expected = $sunday->format('j');
        $this->actual = $crawler->filter('#this-month-holiday-'.$this->expected)->text();
        $this->verify();
    }

    public function testTodayStyle()
    {
        $today = new \DateTime();

        $crawler = $this->client->request('GET', $this->generateUrl('block_calendar'));
        $this->expected = $today->format('j');
        $this->actual = $crawler->filter('#today')->text();
        $this->verify();
    }
}
