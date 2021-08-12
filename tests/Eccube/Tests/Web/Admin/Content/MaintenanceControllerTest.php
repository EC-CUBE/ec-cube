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

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class MaintenanceControllerTest extends AbstractAdminWebTestCase
{
    private $maintenance_file_path;

    public function setUp()
    {
        parent::setUp();

        $this->maintenance_file_path
            = self::$container->getParameter('eccube_content_maintenance_file_path');

        if (file_exists($this->maintenance_file_path)) {
            unlink($this->maintenance_file_path);
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        if (file_exists($this->maintenance_file_path)) {
            unlink($this->maintenance_file_path);
        }
    }

    public function testIndex()
    {
        $crawler = $this->client->request('GET',
            $this->generateUrl('admin_content_maintenance')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertFalse(file_exists($this->maintenance_file_path));
        $this->assertSame('有効にする', $crawler->filter('button.btn-ec-conversion')->text());

        touch($this->maintenance_file_path);

        $crawler = $this->client->request('GET',
            $this->generateUrl('admin_content_maintenance')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertTrue(file_exists($this->maintenance_file_path));
        $this->assertSame('無効にする', $crawler->filter('button.btn-ec-conversion')->text());
    }

    public function testDisableMaintenance()
    {
        touch($this->maintenance_file_path);

        $crawler = $this->client->request('GET',
            $this->generateUrl('admin_content_maintenance')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertTrue(file_exists($this->maintenance_file_path));
        $this->assertSame('無効にする', $crawler->filter('button.btn-ec-conversion')->text());

        $crawler = $this->client->request('POST',
            $this->generateUrl('admin_disable_maintenance', ['mode' => 'manual']),
            [],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $crawler = $this->client->request('GET',
            $this->generateUrl('admin_content_maintenance')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertFalse(file_exists($this->maintenance_file_path));
        $this->assertSame('有効にする', $crawler->filter('button.btn-ec-conversion')->text());
    }
}
