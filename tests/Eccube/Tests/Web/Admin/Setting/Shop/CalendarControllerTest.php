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

class CalendarControllerTest extends AbstractAdminWebTestCase
{

    /**
     * @var CalendarRepository
     */
    protected $calendarRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->calendarRepository = $this->entityManager->getRepository(Calendar::class);
    }

    /**
     * @return Calendar
     */
    public function createCalendar()
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
            'GET',
            $this->generateUrl('admin_setting_shop_calendar_new')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingNew()
    {
        $this->client->request(
            'GET',
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
            'DELETE',
            $this->generateUrl('admin_setting_shop_calendar_delete', ['id' => $id])
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);
        $this->assertSame(true, $actual);
    }

    public function testDeleteFail_NotFound()
    {
        $id = 99999;

        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_setting_shop_calendar_delete', ['id' => $id])
        );
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
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
            'POST',
            $this->generateUrl('admin_setting_shop_calendar'),
            [
                'calendar' => $form,
                'calendar_id' => "$id",
                'mode' => 'edit_inline',
            ]
        );

        $Calendar = $this->calendarRepository->find($id);
        $this->expected = $form['title'];
        $this->actual = $Calendar->getTitle();
        $this->verify();

        $this->expected = $form['holiday'];
        $holiday = $Calendar->getHoliday();
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
            'POST',
            $this->generateUrl('admin_setting_shop_calendar'),
            [
                'calendar' => $form,
            ]
        );

        $Calendar = $this->calendarRepository->find($id + 1);

        $this->expected = $form['title'];
        $this->actual = $Calendar->getTitle();
        $this->verify();

        $this->expected = $form['holiday'];
        $holiday = $Calendar->getHoliday();
        $holiday->setTimezone(new \DateTimeZone('Asia/Tokyo'));
        $this->actual = $holiday->format('Y-n-j');
        $this->verify();
    }
}
