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

namespace Eccube\Tests\ServiceProvider;

use Eccube\Application;
use Eccube\Tests\EccubeTestCase;

class EccubeServiceProviderTest extends EccubeTestCase
{
    /** @var Application $app */
    protected $app;

    public function setUp()
    {
        parent::setUp();
        $this->app = static::createClient()->getContainer()->get('app');
    }

    public function testOrmEm()
    {
        $this->assertInstanceOf(\Doctrine\ORM\EntityManager::class, $this->app['orm.em']);
    }

    public function testConfig()
    {
        $this->expected = 'HMAC';
        $this->actual = $this->app['config']['eccube_auth_type'];
        $this->verify();
    }

    public function testMonolog()
    {
        $this->assertInstanceOf(\Psr\Log\LoggerInterface::class, $this->app['monolog.logger']);
        $this->assertInstanceOf(\Psr\Log\LoggerInterface::class, $this->app['monolog']);
    }

    public function testSession()
    {
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\Session\SessionInterface::class, $this->app['session']);
    }

    public function testFormFactory()
    {
        $this->assertInstanceOf(\Symfony\Component\Form\FormFactoryInterface::class, $this->app['form.factory']);
    }

    public function testSecurity()
    {
        $this->assertInstanceOf(\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface::class, $this->app['security']);
    }

    public function testUser()
    {
        $this->markTestIncomplete('$app[\'user\'] のテストは未実装です');
        $this->assertInstanceOf(\Symfony\Component\Security\Core\User\UserInterface::class, $this->app['user']);
    }

    public function testEventDispatcher()
    {
        $this->assertInstanceOf(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class, $this->app['dispatcher']);
        $this->assertInstanceOf(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class, $this->app['eccube.event.dispatcher']);
    }
}
