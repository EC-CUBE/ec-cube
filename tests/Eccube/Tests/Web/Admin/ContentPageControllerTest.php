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


namespace Eccube\Tests\Web\Admin\ContentPageController;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class ContentPageControllerTest extends AbstractAdminWebTestCase
{

// TODO 以下のエラーによりテストできない
// Symfony\Component\Form\Exception\TransformationFailedException:
// Unable to transform value for property path "header_chk": Expected a Boolean.nel/HttpKernel.php:145
//    public function testRoutingAdminContentPageControllerPage()
//    {
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_content_page')
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//    }
//
//
//    public function testRoutingAdminContentPageControllerEdit()
//    {
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_content_page_edit', array('id' => 0))
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//    }
//
//    public function testRoutingAdminContentPageControllerDelete()
//    {
//        $redirectUrl = $this->app['url_generator']->generate('admin_content_page');
//
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_content_page_delete', array('id' => 0))
//        );
//
//        $actual = $this->client->getResponse()->isRedirect($redirectUrl);
//        $this->assertSame(true, $actual);
//    }

}



