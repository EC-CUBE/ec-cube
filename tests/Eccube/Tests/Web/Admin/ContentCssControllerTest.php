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


namespace Eccube\Tests\Web\Admin\ContentCssController;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class ContentCssControllerTest extends AbstractAdminWebTestCase
{

    public function testRoutingAdminContentCssControllerCss()
    {
        $this->client->request('GET', $this->app['url_generator']
            ->generate('admin_content_css')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentCssControllerDelete()
    {
        $redirectUrl = $this->app['url_generator']->generate('admin_content_css');

        $this->client->request('GET', $this->app['url_generator']
            ->generate('admin_content_css_delete', array('id' => 0))
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);
        $this->assertSame(true, $actual);
    }

}

