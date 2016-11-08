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


namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class MailTemplateControllerTest extends AbstractAdminWebTestCase
{

    public function testRoutingAdminContentMail()
    {
        $client = $this->client;
        $client->request('GET',
            $this->app->url('admin_content_mail')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentMailGet()
    {
        $client = $this->client;

        $client->request('GET',
            $this->app->url('admin_content_mail_edit', array('name' => 'order.twig'))
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }


    public function testRoutingAdminContentMailEdit()
    {
        $client = $this->client;

        $client->request(
            'POST',
            $this->app->url('admin_content_mail_edit', array('name' => 'order.twig')),
            array(
                'admin_mail_template' => array(
                    'tpl_data' => 'testtest',
                    '_token' => 'dummy'
                ),
                'name' => 'order.twig'
            )
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->app->url('admin_content_mail_edit', array('name' => 'order.twig'))));

        $this->expected = 'testtest';
        $this->actual = file_get_contents($this->app['config']['template_realdir'].'/Mail/order.twig');
        $this->verify();
    }


    public function testRoutingAdminContentMailReEdit()
    {
        $client = $this->client;

        $crawler = $client->request(
            'PUT',
            $this->app->url('admin_content_mail_reedit', array('name' => 'order.twig'))
        );

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = file_get_contents($this->app['config']['template_default_realdir'].'/Mail/order.twig');
        $this->actual = file_get_contents($this->app['config']['template_realdir'].'/Mail/order.twig');
        $this->verify();
    }

}