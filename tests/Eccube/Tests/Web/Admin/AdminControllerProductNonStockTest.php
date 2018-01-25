<?php

namespace Eccube\Tests\Web\Admin;

use Symfony\Component\HttpKernel\Client;

/**
 * Class AdminControllerProductNonStockTest.
 */
class AdminControllerProductNonStockTest extends AbstractAdminWebTestCase
{
    /**
     * @var string
     */
    protected $target = '#shop_info';

    /**
     * test redirect when click
     */
    public function testAdminNonStockRedirect()
    {
        $this->client->request('POST', $this->generateUrl('admin_homepage_nonstock'));
        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * test render
     */
    public function testAdminNonStockRender()
    {
        /* @var Client $client */
        $client = $this->client;
        $crawler = $client->request('GET', $this->generateUrl('admin_homepage'));
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertContains('在庫切れ商品', $crawler->filter($this->target)->html());
    }

    /**
     * Test count with search
     *
     * @link https://github.com/EC-CUBE/ec-cube/issues/1898
     */
    public function testAdminNonStockWithSearch()
    {
        $this->markTestIncomplete('Function not implement');

        /* @var Client $client */
        $client = $this->client;
        $crawler = $client->request('GET', $this->generateUrl('admin_homepage'));
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertContains('在庫切れ商品', $crawler->filter($this->target)->html());

        $section = trim($crawler->filter($this->target.' .shop-stock-detail .item_number')->text());
        $this->expected = $showNumber = preg_replace('/\D/', '', $section);

        $client->request('POST', $this->generateUrl('admin_homepage_nonstock'),
                array('admin_search_product' => array('_token' =>  'dummy')));

        $crawler = $client->followRedirect();
        $this->actual = $crawler->filter('.tableish .item_box')->count();

        $this->verify();
    }
}
