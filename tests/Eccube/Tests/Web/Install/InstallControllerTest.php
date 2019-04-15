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


namespace Eccube\Tests\Web\Install;

use Eccube\Tests\Web\Install\AbstractInstallWebTestCase;
use Symfony\Component\Yaml\Yaml;

class InstallControllerTest extends AbstractInstallWebTestCase
{

    public function setUp()
    {
        parent::setUp();
        $config_file = __DIR__.'/../../../../../app/config/eccube/database.yml';
        $this->config = Yaml::parse(file_get_contents($config_file));
    }
    public function testRoutingIndex()
    {
        $this->client->request('GET', $this->app['url_generator']->generate('install'));
        $this->assertTrue($this->client->getResponse()->isRedirection());
    }

    public function testRoutingStep1()
    {
        $this->client->request('GET', $this->app['url_generator']->generate('install_step1'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingStep2()
    {
        $this->client->request('GET', $this->app['url_generator']->generate('install_step2'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingStep3()
    {
        if ($this->config['database']['driver'] == 'pdo_sqlite') {
            $this->markTestSkipped('Can not support for sqlite3');
        }

        $crawler = $this->client->request('GET', $this->app['url_generator']->generate('install_step3'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingStep4()
    {
        if ($this->config['database']['driver'] == 'pdo_sqlite') {
            $this->markTestSkipped('Can not support for sqlite3');
        }

        $this->client->request('GET', $this->app['url_generator']->generate('install_step4'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingStep5()
    {
        $this->client->request('GET', $this->app['url_generator']->generate('install_step5'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingComplete()
    {
        $this->client->request('GET', $this->app['url_generator']->generate('install_complete'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
