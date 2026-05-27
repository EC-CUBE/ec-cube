<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Repository;

use Carbon\Carbon;
use Eccube\Entity\Calendar;
use Eccube\Repository\CalendarRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * CalendarRepository test cases.
 *
 * @author Yuko Kajihara
 */
final class CalendarRepositoryTest extends EccubeTestCase
{
    protected ?\DateTime $DateTimeNow = null;

    protected ?Calendar $Calendar1 = null;

    protected ?Calendar $Calendar2 = null;

    protected ?Calendar $Calendar3 = null;

    protected ?CalendarRepository $calendarRepository = null;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->DateTimeNow = new \DateTime('+1 minutes');
        parent::setUp();
        $this->calendarRepository = $this->entityManager->getRepository(Calendar::class);
        $this->Calendar1 = $this->createCalendar('春分の日', new \DateTime('2021-03-20 00:00:00'));
        $this->Calendar2 = $this->createCalendar('昭和の日', new \DateTime('2021-04-29 00:00:00'));
        $this->Calendar3 = $this->createCalendar('憲法記念日', new \DateTime('2021-05-03 00:00:00'));
        $this->entityManager->flush();
    }

    /**
     * Create Calendar entity
     */
    public function createCalendar(string $title = 'title', ?\DateTime $holiday = null): Calendar
    {
        /** @var Calendar $Calendar */
        $Calendar = new Calendar();
        if (is_null($holiday)) {
            $holiday = $this->DateTimeNow;
        }
        $Calendar->setTitle($title)
            ->setHoliday($holiday);
        $this->entityManager->persist($Calendar);
        $this->entityManager->flush();

        return $Calendar;
    }

    public function testGetListOrderByIdDesc()
    {
        $Calendars = $this->calendarRepository->getListOrderByIdDesc();

        // IDは自動採番で環境により番号が異なるので、登録されたタイトル降順で確認
        $this->expected = 3;
        $this->actual = count($Calendars);
        $this->verify();

        $this->expected = '憲法記念日';
        $this->actual = $Calendars[0]->getTitle();
        $this->verify();

        $this->expected = '昭和の日';
        $this->actual = $Calendars[1]->getTitle();
        $this->verify();

        $this->expected = '春分の日';
        $this->actual = $Calendars[2]->getTitle();
        $this->verify();
    }

    public function testGetHolidayList()
    {
        $Calendars = $this->calendarRepository->getHolidayList(Carbon::parse('2021-03-01'), Carbon::parse('2021-04-30'));

        $this->expected = 2;
        $this->actual = count($Calendars);
        $this->verify();

        $this->expected = '春分の日';
        $this->actual = $Calendars[0]->getTitle();
        $this->verify();

        $this->expected = '昭和の日';
        $this->actual = $Calendars[1]->getTitle();
        $this->verify();
    }

    public function testDelete()
    {
        $this->calendarRepository->delete($this->Calendar2);
        $Results = $this->calendarRepository->findAll();

        $this->expected = 2;
        $this->actual = count($Results);
        $this->verify();
    }
}
