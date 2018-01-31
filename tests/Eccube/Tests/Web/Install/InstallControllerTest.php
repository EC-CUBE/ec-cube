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


namespace Eccube\Tests\Web\Install;

use Eccube\Common\Constant;
use Eccube\Tests\Web\AbstractWebTestCase;
use Eccube\Controller\Install\InstallController;
use Eccube\Security\Core\Encoder\PasswordEncoder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class InstallControllerTest extends AbstractWebTestCase
{

    /**
     * @var InstallController
     */
    protected $controller;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Session
     */
    protected $session;

    public function setUp()
    {
        parent::setUp();
        $formFactory = $this->container->get('form.factory');
        $encoder = $this->container->get(PasswordEncoder::class);
        $config = $this->container->getParameter('eccube.constants');
        $this->session = new Session(new MockArraySessionStorage());
        $this->controller = new InstallController($this->session, $formFactory, $encoder, 'install', $config);
        $reflectionClass = new \ReflectionClass($this->controller);
        $propContainer = $reflectionClass->getProperty('container');
        $propContainer->setAccessible(true);
        $propContainer->setValue($this->controller, $this->container);

        $this->request = $this->createMock(Request::class);
    }

    public function testIndex()
    {
        $this->assertInstanceOf(RedirectResponse::class, $this->controller->index($this->request));
    }

    public function testStep1()
    {
        $this->actual = $this->controller->step1($this->request);
        $this->assertTrue(is_array($this->actual));
        $this->assertInstanceOf(FormView::class, $this->actual['form']);
    }

    public function testStep2()
    {
        $this->actual = $this->controller->step2($this->request);
        $this->assertArrayHasKey('protectedDirs', $this->actual);
    }

    public function testStep3()
    {
        $this->actual = $this->controller->step3($this->request);
        $this->assertTrue(is_array($this->actual));
        $this->assertInstanceOf(FormView::class, $this->actual['form']);
        $this->assertInstanceOf(Request::class, $this->actual['request']);
    }

    public function testStep4()
    {
        $this->actual = $this->controller->step4($this->request);
        $this->assertTrue(is_array($this->actual));
        $this->assertInstanceOf(FormView::class, $this->actual['form']);
    }

    public function testComplete()
    {
        $this->session->set('eccube.session.install',
                            [
                                'authmagic' => 'secret',
                                'admin_allow_hosts' => "'[\"127.0.0.1\",\"192.168.0.1\"]'"
                            ]);
        $this->actual = $this->controller->complete($this->request);
        $this->assertArrayHasKey('admin_url', $this->actual);
    }

    public function testCreateDatabaseUrl()
    {
        $params = [
            'database' => 'pdo_sqlite',
            'database_name' => '/foo/bar/eccube.db'
        ];
        $this->expected = 'sqlite:///foo/bar/eccube.db';
        $this->actual = $this->controller->createDatabaseUrl($params);
        $this->verify();

        $params = [
            'database' => 'pdo_mysql',
            'database_name' => 'cube4_dev',
            'database_host' => 'localhost'
        ];
        $this->expected = 'mysql://localhost/cube4_dev';
        $this->actual = $this->controller->createDatabaseUrl($params);
        $this->verify();

        $params = [
            'database' => 'pdo_pgsql',
            'database_name' => 'cube4_dev',
            'database_host' => 'localhost',
            'database_port' => '5432'
        ];
        $this->expected = 'pgsql://localhost:5432/cube4_dev';
        $this->actual = $this->controller->createDatabaseUrl($params);
        $this->verify();

        $params = [
            'database' => 'pdo_pgsql',
            'database_name' => 'cube4_dev',
            'database_host' => 'localhost',
            'database_port' => '5432',
            'database_user' => 'postgres'
        ];
        $this->expected = 'pgsql://postgres@localhost:5432/cube4_dev';
        $this->actual = $this->controller->createDatabaseUrl($params);
        $this->verify();

        $params = [
            'database' => 'pdo_mysql',
            'database_name' => 'cube4_dev',
            'database_host' => 'localhost',
            'database_port' => '3306',
            'database_user' => 'root',
            'database_password' => 'password'
        ];
        $this->expected = 'mysql://root:password@localhost:3306/cube4_dev';
        $this->actual = $this->controller->createDatabaseUrl($params);
        $this->verify();
    }

    public function testExtractDatabaseUrl()
    {
        $url = 'sqlite:///foo/bar/eccube.db';
        $this->expected = [
            'database' => 'pdo_sqlite',
            'database_name' => '/foo/bar/eccube.db'
        ];
        $this->actual = $this->controller->extractDatabaseUrl($url);
        $this->verify();

        $url = 'mysql://root:password@localhost:3306/cube4_dev';
        $this->actual = $this->controller->extractDatabaseUrl($url);
        $this->expected = [
            'database' => 'pdo_mysql',
            'database_name' => 'cube4_dev',
            'database_host' => 'localhost',
            'database_port' => '3306',
            'database_user' => 'root',
            'database_password' => 'password'
        ];
        $this->verify();

        $url = 'mysql://root:password@localhost/cube4_dev';
        $this->actual = $this->controller->extractDatabaseUrl($url);
        $this->expected = [
            'database' => 'pdo_mysql',
            'database_name' => 'cube4_dev',
            'database_host' => 'localhost',
            'database_port' => null,
            'database_user' => 'root',
            'database_password' => 'password'
        ];
        $this->verify();

        $url = 'mysql://root@localhost/cube4_dev';
        $this->actual = $this->controller->extractDatabaseUrl($url);
        $this->expected = [
            'database' => 'pdo_mysql',
            'database_name' => 'cube4_dev',
            'database_host' => 'localhost',
            'database_port' => null,
            'database_user' => 'root',
            'database_password' => null
        ];
        $this->verify();

        $url = 'pgsql://localhost/cube4_dev';
        $this->actual = $this->controller->extractDatabaseUrl($url);
        $this->expected = [
            'database' => 'pdo_pgsql',
            'database_name' => 'cube4_dev',
            'database_host' => 'localhost',
            'database_port' => null,
            'database_user' => null,
            'database_password' => null
        ];
        $this->verify();
    }

    public function testCreateMailerUrl()
    {
        $params = [
            'smtp_host' => 'localhost'
        ];
        $this->expected = 'smtp://localhost';
        $this->actual = $this->controller->createMailerUrl($params);
        $this->verify();

        $params = [
            'smtp_host' => 'localhost',
            'smtp_port' => 587
        ];
        $this->expected = 'smtp://localhost:587';
        $this->actual = $this->controller->createMailerUrl($params);
        $this->verify();

        $params = [
            'smtp_host' => 'localhost',
            'smtp_port' => 587,
            'smtp_password' => 'password',
            'smtp_username' => 'username'
        ];
        $this->expected = 'smtp://username:password@localhost:587?auth_mode=plain';
        $this->actual = $this->controller->createMailerUrl($params);
        $this->verify();

        $params = [
            'smtp_host' => 'localhost',
            'smtp_port' => 587,
            'smtp_password' => 'password',
            'auth_mode' => 'login',
            'smtp_username' => 'username'
        ];
        $this->expected = 'smtp://username:password@localhost:587?auth_mode=login';
        $this->actual = $this->controller->createMailerUrl($params);
        $this->verify();

        $params = [
            'smtp_host' => 'localhost',
            'smtp_port' => 465,
            'smtp_password' => 'password',
            'auth_mode' => 'login',
            'encryption' => 'ssl',
            'smtp_username' => 'username'
        ];
        $this->expected = 'smtp://username:password@localhost:465?auth_mode=login&encryption=ssl';
        $this->actual = $this->controller->createMailerUrl($params);
        $this->verify();

        $params = [
            'smtp_host' => 'localhost',
            'encryption' => 'ssl'
        ];
        $this->expected = 'smtp://localhost:465?encryption=ssl';
        $this->actual = $this->controller->createMailerUrl($params);
        $this->verify();

        $params = [
            'transport' => 'gmail',
            'smtp_host' => 'smtp.gmail.com',
            'encryption' => 'ssl',
            'auth_mode' => 'login',
            'smtp_password' => 'password',
            'smtp_username' => 'username@gmail.com'
        ];
        $this->expected = 'gmail://username@gmail.com:password@smtp.gmail.com:465?auth_mode=login&encryption=ssl';
        $this->actual = $this->controller->createMailerUrl($params);
        $this->verify();
    }

    public function testExtractMailerUrl()
    {
        $url = 'smtp://localhost';
        $this->actual = $this->controller->extractMailerUrl($url);
        $this->expected = [
            'transport' => 'smtp',
            'smtp_password' => null,
            'smtp_host' => 'localhost',
            'smtp_port' => 25,
            'encryption' => null,
            'auth_mode' => null,
            'smtp_username' => null
        ];
        $this->verify();

        $url = 'smtp://localhost:587';
        $this->actual = $this->controller->extractMailerUrl($url);
        $this->expected = [
            'transport' => 'smtp',
            'smtp_password' => null,
            'smtp_host' => 'localhost',
            'smtp_port' => 587,
            'encryption' => null,
            'auth_mode' => null,
            'smtp_username' => null,
        ];
        $this->verify();

        $url = 'smtp://username:password@localhost:587';
        $this->actual = $this->controller->extractMailerUrl($url);
        $this->expected = [
            'transport' => 'smtp',
            'smtp_username' => 'username',
            'smtp_password' => 'password',
            'smtp_host' => 'localhost',
            'smtp_port' => 587,
            'encryption' => null,
            'auth_mode' => 'plain'
        ];
        $this->verify();

        $url = 'smtp://username:password@localhost:587?auth_mode=login';
        $this->actual = $this->controller->extractMailerUrl($url);
        $this->expected = [
            'transport' => 'smtp',
            'smtp_username' => 'username',
            'smtp_password' => 'password',
            'smtp_host' => 'localhost',
            'smtp_port' => 587,
            'encryption' => null,
            'auth_mode' => 'login'
        ];
        $this->verify();

        $url = 'smtp://username:password@localhost:587?auth_mode=plain&encryption=tls';
        $this->actual = $this->controller->extractMailerUrl($url);
        $this->expected = [
            'transport' => 'smtp',
            'smtp_username' => 'username',
            'smtp_password' => 'password',
            'smtp_host' => 'localhost',
            'smtp_port' => 587,
            'encryption' => 'tls',
            'auth_mode' => 'plain'
        ];
        $this->verify();

        $url = 'gmail://username@gmail.com:password@smtp.gmail.com:465?auth_mode=login&encryption=ssl';
        $this->actual = $this->controller->extractMailerUrl($url);
        $this->expected = [
            'transport' => 'smtp',
            'smtp_username' => 'username@gmail.com',
            'smtp_password' => 'password',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 465,
            'encryption' => 'ssl',
            'auth_mode' => 'login'
        ];
        $this->verify();
    }

    public function testDatabaseVersion()
    {
        $version = $this->controller->getDatabaseVersion($this->entityManager);
        $this->assertRegExp('/[0-9.]+/', $version);
    }

    public function testCreateAppData()
    {
        $params = [
            'http_url' => 'http://example.com',
            'shop_name' => 'example shop'
        ];
        $appData = $this->controller->createAppData($params, $this->entityManager);

        $this->assertEquals('http://example.com', $appData['site_url']);
        $this->assertEquals('example shop', $appData['shop_name']);
        $this->assertEquals(Constant::VERSION, $appData['cube_ver']);
        $this->assertEquals(phpversion(), $appData['php_ver']);
        $this->assertEquals(php_uname(), $appData['os_type']);
        $this->assertRegExp('/(sqlite|mysql|postgresql).[0-9.]+/', $appData['db_ver']);
    }

    public function testConvertAdminAllowHosts()
    {
        $adminAllowHosts = "127.0.0.1\r\n192.168.0.1";
        $this->expected = '\'["127.0.0.1","192.168.0.1"]\'';
        $this->actual = $this->controller->convertAdminAllowHosts($adminAllowHosts);
        $this->verify();
    }
}
