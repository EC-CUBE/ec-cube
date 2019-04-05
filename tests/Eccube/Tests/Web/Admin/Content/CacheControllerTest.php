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


namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class CacheControllerTest extends AbstractAdminWebTestCase
{

    public function testRoutingAdminContentCache()
    {
        $client = $this->client;
        $client->request('GET',
            $this->app->url('admin_content_cache')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentCachePost()
    {
        $client = $this->client;

        $url = $this->app->url('admin_content_cache');

        $cacheDir = $this->app['config']['root_dir'].'/app/cache';
        file_put_contents($cacheDir.'/twig/sample', 'test');

        // makes the POST request
        $crawler = $client->request('POST', $url, array(
                'admin_cache' => array(
                    '_token' => 'dummy',
                    'cache' => array('twig'),
            ),
        ));

        $this->assertFalse(file_exists($cacheDir.'/twig/sample'), 'sampleは削除済');
    }

}