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
use Eccube\Repository\CalendarRepository;
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
        $this->expected = Carbon::now()->format('Y年n月');
        $this->actual = $crawler->filter('#this-month-title')->text();
        $this->verify();
    }

    public function testNextMonthTitle()
    {
        $crawler = $this->client->request('GET', $this->generateUrl('block_calendar'));
        $this->expected = Carbon::now()->addMonth(1)->format('Y年n月');
        $this->actual = $crawler->filter('#next-month-title')->text();
        $this->verify();
    }

    public function testTodayAndHolidayStile()
    {
        $today = new \DateTime();
        $Calendar = new Calendar();
        $Calendar->setTitle('今日かつ定休日のパターン')
            ->setHoliday($today);
        $this->container->get(CalendarRepository::class);
        $this->entityManager->persist($Calendar);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', $this->generateUrl('block_calendar'));
        $this->expected = $today->format('j');
        $this->actual = $crawler->filter('#today-and-holiday')->text();
        $this->verify();
    }

    public function testHolidayStile()
    {
        $holiday = new \DateTime(Carbon::now()->format('Y-m').'-01');
        $Calendar = new Calendar();
        $Calendar->setTitle('今月の月初が定休日のパターン')
            ->setHoliday($holiday);
        $this->container->get(CalendarRepository::class);
        $this->entityManager->persist($Calendar);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', $this->generateUrl('block_calendar'));
        $this->expected = '1';
        $this->actual = $crawler->filter('.ec-calendar__holiday')->text();
        $this->verify();
    }

    public function testTodayStile()
    {
        $today = new \DateTime();

        $crawler = $this->client->request('GET', $this->generateUrl('block_calendar'));
        $this->expected = $today->format('j');
        $this->actual = $crawler->filter('#today')->text();
        $this->verify();
    }
}
