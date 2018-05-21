<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * @group cache-clear
 */
class CacheControllerTest extends AbstractAdminWebTestCase
{
    public function testRoutingAdminContentCache()
    {
        $client = $this->client;
        $client->request('GET',
            $this->generateUrl('admin_content_cache')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminContentCachePost()
    {
        $client = $this->client;

        $url = $this->generateUrl('admin_content_cache');

        $cacheDir = $this->container->getParameter('kernel.cache_dir');
        file_put_contents($cacheDir.'/twig/sample', 'test');

        $crawler = $client->request('POST', $url, [
            'form' => [
                '_token' => 'dummy',
            ],
        ]);

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertFalse(file_exists($cacheDir.'/twig/sample'), 'sampleは削除済');
    }
}
