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

class BlockControllerTest extends AbstractAdminWebTestCase
{

    public function tearDown()
    {
        parent::tearDown();
    }
    public function test_routing_AdminContentBlock_index()
    {

        $this->client->request('GET', $this->app->url('admin_content_block'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routing_AdminContentBlock_edit()
    {
        $this->client->request('GET',
            $this->app->url(
                'admin_content_block_edit',
                array('id' => 1)
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function test_routing_AdminContentBlock_editWithPost()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_content_block_edit', array('id' => 1)),
            array('block' => array(
                'name' => 'newblock',
                'file_name' => 'file_name',
                'block_html' => '<p>test</p>',
                'DeviceType' => 1,
                'id' => 1,
                '_token' => 'token'
            ))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->app->url('admin_content_block_edit', array('id' => 1))
        ));

        $this->expected = '<p>test</p>';
        $this->actual = file_get_contents($this->app['config']['block_realdir'].'/file_name.twig');
        $this->verify();

        // Filesystem::dumpFile() がエラーになるので bovigo\vfs が使用できない
        if (file_exists($this->app['config']['block_realdir'].'/file_name.twig')) {
            unlink($this->app['config']['block_realdir'].'/file_name.twig');
        }
    }


    public function test_routing_AdminContentBlock_defaultBlockDelete()
    {

        $redirectUrl = $this->app->url('admin_content_block');

        $this->client->request('DELETE',
            $this->app->url(
                'admin_content_block_delete',
                array('id' => 1)
            )
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }

}
