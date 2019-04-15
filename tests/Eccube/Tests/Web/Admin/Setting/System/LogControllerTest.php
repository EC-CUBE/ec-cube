<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
 * @package Eccube\Tests\Web\Admin\Setting\System
 */
class LogControllerTest extends AbstractAdminWebTestCase
{
    protected $logTest;

    protected $formData;

    /**
     * Set up
     */
    public function setUp()
    {
        parent::setUp();
        $this->formData = array(
            '_token' => 'dummy',
            'files' => 'site_'.date('Y-m-d').'.test.log',
            'line_max' => '50',
        );
        $this->logTest = $this->app['config']['root_dir'].'/app/log/'.$this->formData['files'];
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
            $this->app->url('admin_setting_system_log')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * change log
     */
    public function testSystemLogSubmit()
    {
        $this->createTestFile(1);

        $this->client->request(
            'POST',
            $this->app->url('admin_setting_system_log'),
            array('admin_system_log' => $this->formData)
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

        /** @var $crawler Crawler*/
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_setting_system_log'),
            array('admin_system_log' => $this->formData)
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        list($this->actual) = $crawler->filter('#line-max')->extract(array('style'));
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
        return array(
            array('', 'background-color:#ffe8e8;', '※ 入力されていません。'),
            array('a', 'background-color:#ffe8e8;', '※ 有効な数字ではありません。'),
            array(-1, 'background-color:#ffe8e8;', '※ 0以上でなければなりません。'),
            array(0, '', ''),
            array(50000, '', ''),
            array(1.1, '', ''),
            array(100001, 'background-color:#ffe8e8;', '※ 50000以下でなければなりません。'),
        );
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
