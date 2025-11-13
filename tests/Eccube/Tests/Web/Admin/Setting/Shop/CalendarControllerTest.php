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

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Entity\Calendar;
use Eccube\Repository\CalendarRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarControllerTest extends AbstractAdminWebTestCase
{
    protected ?CalendarRepository $calendarRepository = null;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->calendarRepository = $this->entityManager->getRepository(Calendar::class);
    }

    public function createCalendar(): Calendar
    {
        $TargetCalendar = new Calendar();
        $TargetCalendar->setTitle('春分の日')
            ->setHoliday(new \DateTime('2021-03-20 00:00:00'));
        $this->entityManager->persist($TargetCalendar);
        $this->entityManager->flush();

        return $TargetCalendar;
    }

    public function testRouting()
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_setting_shop_calendar_new')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingNew()
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('admin_setting_shop_calendar')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testDeleteSuccess()
    {
        $Calendar = $this->createCalendar();
        $id = $Calendar->getId();

        $redirectUrl = $this->generateUrl('admin_setting_shop_calendar');

        $this->client->request(
            Request::METHOD_DELETE,
            $this->generateUrl('admin_setting_shop_calendar_delete', ['id' => $id])
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);
        $this->assertTrue($actual);
    }

    public function testDeleteFailNotFound()
    {
        $id = 99999;

        $this->client->request(
            Request::METHOD_DELETE,
            $this->generateUrl('admin_setting_shop_calendar_delete', ['id' => $id])
        );
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function testEditSuccess()
    {
        $Calendar = $this->createCalendar();
        $id = $Calendar->getId();

        $form = [
            '_token' => 'dummy',
            'title' => '昭和の日',
            'holiday' => '2021-4-29',
        ];

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop_calendar'),
            [
                'calendar' => $form,
                'calendar_id' => "$id",
                'mode' => 'edit_inline',
            ]
        );

        $Calendar = $this->calendarRepository->find($id);
        $this->expected = $form['title'];
        $this->assertInstanceOf(Calendar::class, $Calendar);
        $this->actual = $Calendar->getTitle();
        $this->verify();

        $this->expected = $form['holiday'];
        $this->assertInstanceOf(Calendar::class, $Calendar);
        $holiday = $Calendar->getHoliday();
        $this->assertInstanceOf(\DateTime::class, $holiday);
        $holiday->setTimezone(new \DateTimeZone('Asia/Tokyo'));
        $this->actual = $holiday->format('Y-n-j');
        $this->verify();
    }

    public function testNewSuccess()
    {
        $Calendar = $this->createCalendar();
        $id = $Calendar->getId();
        $form = [
            '_token' => 'dummy',
            'title' => '憲法記念日',
            'holiday' => '2021-5-3',
        ];

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop_calendar'),
            [
                'calendar' => $form,
            ]
        );

        $Calendar = $this->calendarRepository->find($id + 1);

        $this->expected = $form['title'];
        $this->assertInstanceOf(Calendar::class, $Calendar);
        $this->actual = $Calendar->getTitle();
        $this->verify();

        $this->expected = $form['holiday'];
        $this->assertInstanceOf(Calendar::class, $Calendar);
        $holiday = $Calendar->getHoliday();
        $this->assertInstanceOf(\DateTime::class, $holiday);
        $holiday->setTimezone(new \DateTimeZone('Asia/Tokyo'));
        $this->actual = $holiday->format('Y-n-j');
        $this->verify();
    }
}
