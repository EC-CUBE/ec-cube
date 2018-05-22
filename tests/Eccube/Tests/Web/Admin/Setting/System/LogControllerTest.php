<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Tests\Web\Admin\Setting\System;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Faker\Generator;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class LogControllerTest
 */
class LogControllerTest extends AbstractAdminWebTestCase
{
    /** log Test   */
    protected $logTest;

    /** form Data   */
    protected $formData;

    public function setUp()
    {
        parent::setUp();

        $this->formData = [
            '_token' => 'dummy',
            'files' => 'site_'.date('Y-m-d').'.log',
            'line_max' => '50',
        ];

        $logDir = $this->container->getParameter('kernel.logs_dir');

        $this->logTest = $logDir.'/'.$this->formData['files'];

        if (!file_exists($this->logTest)) {
            file_put_contents($this->logTest, 'test');
        }
    }

    /**
     * rollback
     */
    public function tearDown()
    {
        parent::tearDown();
        if (file_exists($this->logTest)) {
            unlink($this->logTest);
        }
    }

    /**
     * routing
     */
    public function testRoutingAdminSettingSystemLog()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_setting_system_log')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * change log
     */
    public function testSystemLogSubmit()
    {
        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_system_log'),
            ['admin_system_log' => $this->formData]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Validate test.
     *
     * @param string|int $value
     * @param string     $expected
     * @param string     $message
     * @dataProvider dataProvider
     */
    public function testSystemLogValidate($value, $expected, $message)
    {
        $this->createTestFile(1);

        $this->formData['line_max'] = $value;

        /** @var $crawler Crawler */
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_system_log'),
            ['admin_system_log' => $this->formData]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        list($this->actual) = $crawler->filter('#line-max')->extract(['style']);
        $this->expected = $expected;
        $this->verify();
        if ($message) {
            $this->assertContains($message, $crawler->filter('#log_conditions_box__body')->html());
        }
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            ['', '', '入力されていません。'],
            ['a', '', '有効な数字ではありません。'],
            [-1, '', '0以上でなければなりません。'],
            [0, '', ''],
            [50000, '', ''],
            [1.1, '', ''],
            [100001, '', '50000以下でなければなりません。'],
        ];
    }

    private function createTestFile($number)
    {
        /** @var $faker Generator */
        $faker = $this->getFaker();

        if (!file_exists($this->logTest)) {
            file_put_contents($this->logTest, $faker->paragraphs($number));
        }
    }
}
