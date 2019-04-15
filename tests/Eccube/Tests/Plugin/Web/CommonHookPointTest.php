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

namespace Eccube\Tests\Plugin\Web;

use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\Plugin\CommonHookPoint\HookPointEvent;

/**
 * 共通フックポイント検証用のテストケース
 *
 * CommonHookPointプラグインをロードし、WebTestで画面を巡回させる
 * フックポイントごとにテストするため、本体のプラグインロード処理を使わず、addListenerで直接イベントクラス登を録している
 *
 * CommonHookPointプラグインの各メソッドは、自身のフックポイント名または、引数で渡されるeventオブジェクトのクラス名をechoしている
 * テストケース側では、出力された内容を`expectOutputString`メソッドで確認し、フックポイントの呼び出しが行われているかを検証する
 *
 * また、イベントオブジェクトを利用してリダイレクトできるかどうかも検証する
 *
 * @package Eccube\Tests\Plugin\Web
 */
class CommonHookPointTest extends EccubeTestCase
{
    protected $event;

    public function setUp()
    {
        parent::setUp();

        $this->event = new HookPointEvent($this->app);
        $this->client = $this->createClient();
    }

    public function testAppBefore()
    {
        $hookpoint = 'eccube.event.app.before';
        $listener =  array($this->event, 'onAppBefore');
        $this->app['eccube.event.dispatcher']->addListener($hookpoint, $listener);

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expectOutputString($hookpoint);

        $this->app['eccube.event.dispatcher']->removeListener($hookpoint, $this->event);
    }

    public function testAppBeforeWithArgument()
    {
        $hookpoint = 'eccube.event.app.before';
        $listener = array($this->event, 'onAppBeforeWithArgument');
        $this->app['eccube.event.dispatcher']->addListener($hookpoint, $listener);

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expectOutputString('Symfony\Component\HttpKernel\Event\GetResponseEvent');

        $this->app['eccube.event.dispatcher']->removeListener($hookpoint, $listener);
    }

    public function testControllerHomepageBefore()
    {
        $hookpoint = 'eccube.event.controller.homepage.before';
        $listener = array($this->event, 'onControllerHomepageBefore');
        $this->app['eccube.event.dispatcher']->addListener($hookpoint, $listener);

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expectOutputString($hookpoint);

        $this->app['eccube.event.dispatcher']->removeListener($hookpoint, $listener);
    }

    public function testControllerHomepageBeforeWithArgument()
    {
        $hookpoint = 'eccube.event.controller.homepage.before';
        $listener = array($this->event, 'onControllerHomepageBeforeWithArgument');
        $this->app['eccube.event.dispatcher']->addListener($hookpoint, $listener);

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expectOutputString('Symfony\Component\HttpKernel\Event\GetResponseEvent');

        $this->app['eccube.event.dispatcher']->removeListener($hookpoint, $listener);
    }

    public function testControllerHomepageAfter()
    {
        $hookpoint = 'eccube.event.controller.homepage.after';
        $listener = array($this->event, 'onControllerHomepageAfter');
        $this->app['eccube.event.dispatcher']->addListener($hookpoint, $listener);

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expectOutputString($hookpoint);

        $this->app['eccube.event.dispatcher']->removeListener($hookpoint, $listener);
    }

    public function testControllerHomepageAfterWithArgument()
    {
        $hookpoint = 'eccube.event.controller.homepage.after';
        $listener = array($this->event, 'onControllerHomepageAfterWithArgument');
        $this->app['eccube.event.dispatcher']->addListener($hookpoint, $listener);

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expectOutputString('Symfony\Component\HttpKernel\Event\FilterResponseEvent');

        $this->app['eccube.event.dispatcher']->removeListener($hookpoint, $listener);
    }

    public function testAppAfter()
    {
        $hookpoint = 'eccube.event.app.after';
        $listener = array($this->event, 'onAppAfter');
        $this->app['eccube.event.dispatcher']->addListener($hookpoint, $listener);

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expectOutputString($hookpoint);

        $this->app['eccube.event.dispatcher']->removeListener($hookpoint, $listener);
    }

    public function testAppAfterWithArgument()
    {
        $hookpoint = 'eccube.event.app.after';
        $listener = array($this->event, 'onAppAfterWithArgument');
        $this->app['eccube.event.dispatcher']->addListener($hookpoint, $listener);

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expectOutputString('Symfony\Component\HttpKernel\Event\FilterResponseEvent');

        $this->app['eccube.event.dispatcher']->removeListener($hookpoint, $listener);
    }

    public function testControllerHomepageFinish()
    {
        $hookpoint = 'eccube.event.controller.homepage.finish';
        $listener = array($this->event, 'onControllerHomepageFinish');
        $this->app['eccube.event.dispatcher']->addListener($hookpoint, $listener);

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expectOutputString($hookpoint);

        $this->app['eccube.event.dispatcher']->removeListener($hookpoint,$listener);
    }

    public function testControllerHomepageFinishWithArgument()
    {
        $hookpoint = 'eccube.event.controller.homepage.finish';
        $listener = array($this->event, 'onControllerHomepageFinishWithArgument');
        $this->app['eccube.event.dispatcher']->addListener($hookpoint, $listener);

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->expectOutputString('Symfony\Component\HttpKernel\Event\PostResponseEvent');

        $this->app['eccube.event.dispatcher']->removeListener($hookpoint,$listener);
    }

    public function testAppBeforeRedirect()
    {
        $hookpoint = 'eccube.event.app.before';
        $listener = array($this->event, 'onAppBeforeRedirect');
        $this->app['eccube.event.dispatcher']->addListener($hookpoint, $listener);

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->app['eccube.event.dispatcher']->removeListener($hookpoint, $listener);
    }

    public function testControllerHomepageBeforeRedirect()
    {
        $hookpoint = 'eccube.event.controller.homepage.before';
        $listener = array($this->event, 'onControllerHomepageBeforeRedirect');
        $this->app['eccube.event.dispatcher']->addListener($hookpoint, $listener);

        $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $this->app['eccube.event.dispatcher']->removeListener($hookpoint, $listener);
    }
}
