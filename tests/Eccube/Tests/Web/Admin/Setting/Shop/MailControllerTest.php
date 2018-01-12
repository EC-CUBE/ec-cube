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


namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * Class MailControllerTest
 * @package Eccube\Tests\Web\Admin\Setting\Shop
 */
class MailControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    /**
     * @return mixed
     */
    public function createMail()
    {
        $faker = $this->getFaker();
        // create new mail
        $Mail = $this->app['orm.em']->getRepository('Eccube\Entity\MailTemplate')->findOrCreate(0);
        $Mail->setName($faker->word);
        $Mail->setFileName('Mail/order.twig');
        $Mail->setMailSubject($faker->word);
        $Mail->setMailHeader($faker->word);
        $Mail->setMailFooter($faker->word);
        $this->app['orm.em']->persist($Mail);
        $this->app['orm.em']->flush();

        return $Mail;
    }

    /**
     * Routing
     */
    public function testRouting()
    {
        $this->client->request('GET', $this->app->url('admin_setting_shop_mail'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Edit
     */
    public function testRoutingEdit()
    {
        $MailTemplate = $this->createMail();
        $this->client->request('GET',
            $this->app->url('admin_setting_shop_mail_edit', array('id' => $MailTemplate->getId()))
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Edit
     */
    public function testEdit()
    {
        $MailTemplate = $this->createMail();
        $form = array(
            '_token' => 'dummy',
            'template' => $MailTemplate->getId(),
            'mail_subject' => 'Test Subject',
            'mail_header' => 'Test Header',
            'mail_footer' => 'Test Footer',
        );
        $this->client->request(
            'POST',
            $this->app->url('admin_setting_shop_mail_edit', array('id' => $MailTemplate->getId())),
            array('mail' => $form)
        );

        $redirectUrl = $this->app->url('admin_setting_shop_mail_edit', array('id' => $MailTemplate->getId()));
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = $form['mail_subject'];
        $this->expected = $MailTemplate->getMailSubject();
        $this->verify();
    }

    public function testEditFail()
    {
        $mid = 99999;
        $form = array(
            '_token' => 'dummy',
            'template' => $mid,
            'mail_subject' => 'Test Subject',
            'mail_header' => 'Test Header',
            'mail_footer' => 'Test Footer',
        );
        $this->client->request(
            'POST',
            $this->app->url('admin_setting_shop_mail_edit', array('id' => $mid)),
            array('mail' => $form)
        );

        $redirectUrl = $this->app->url('admin_setting_shop_mail');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $outPut = $this->app['session']->getFlashBag()->get('eccube.admin.error');
        $this->actual = array_shift($outPut);
        $this->expected = 'admin.shop.mail.save.error';
        $this->verify();
    }

    /**
     * Create
     */
    public function testCreateFail()
    {
        $form = array(
            '_token' => 'dummy',
            'template' => null,
            'mail_subject' => null,
            'mail_header' => null,
            'mail_footer' => null,
        );
        $this->client->request(
            'POST',
            $this->app->url('admin_setting_shop_mail'),
            array('mail' => $form)
        );

        $redirectUrl = $this->app->url('admin_setting_shop_mail');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }
}
