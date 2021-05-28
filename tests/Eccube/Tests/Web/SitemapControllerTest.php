<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web;

class SitemapControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        $this->client->request('GET', $this->generateUrl('sitemap_xml'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testProduct()
    {
        $this->client->request('GET', $this->generateUrl('sitemap_product_xml', ['page' => 1]));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testProduct404()
    {
        $this->client->request('GET', $this->generateUrl('sitemap_product_xml', ['page' => 9999]));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testCategory()
    {
        $this->client->request('GET', $this->generateUrl('sitemap_category_xml'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testPage()
    {
        $this->client->request('GET', $this->generateUrl('sitemap_page_xml'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
