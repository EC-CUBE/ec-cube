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

use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\PageLayout;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class PageControllerTest extends AbstractAdminWebTestCase
{

    public function test_routing_AdminContentPage_index()
    {
        $this->client->request('GET', $this->app->url('admin_content_page'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routing_AdminContentPage_edit()
    {
        $this->client->request('GET',
            $this->app->url(
                'admin_content_page_edit',
                array('id' => 1)
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routing_AdminContentPage_delete()
    {

        $redirectUrl = $this->app->url('admin_content_page');

        $this->client->request('DELETE',
            $this->app->url(
                'admin_content_page_delete',
                array('id' => 1)
            )
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertTrue($actual);
    }

    public function test_routing_AdminContentPage_delete_flg_user()
    {

        $redirectUrl = $this->app->url('admin_content_page');

        $DeviceType = $this->app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayout = new PageLayout();
        $PageLayout->setDeviceType($DeviceType);
        $PageLayout->setEditFlg(PageLayout::EDIT_FLG_USER);
        $PageLayout->setUrl('hogehoge');
        $this->app['orm.em']->persist($PageLayout);
        $this->app['orm.em']->flush();

        $this->client->request('DELETE',
            $this->app->url(
                'admin_content_page_delete',
                array('id' => $PageLayout->getId())
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

    }

}
