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
        $this->session = new Session(new MockArraySessionStorage());
        $this->controller = new InstallController($this->session, $formFactory, $encoder, 'install');
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
        $this->session->set('eccube.session.install', ['ECCUBE_AUTH_MAGIC' => 'secret']);
        $this->actual = $this->controller->complete($this->request);
        $this->assertArrayHasKey('admin_url', $this->actual);
    }
}
