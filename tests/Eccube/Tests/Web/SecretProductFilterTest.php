<?php


namespace Eccube\Tests\Web;


use Symfony\Component\HttpKernel\Client;

class SecretProductFilterTest extends AbstractWebTestCase
{

    public function testGuestCannotSeeDetailPageOfSecretProduct()
    {
        $Product = $this->createProduct('テスト');

        /** @var Client $client */
        $client = $this->client;
        $client->request('POST',
            $this->app->url('product_detail', array('id' => $Product->getId()))
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testCustomerCanSeeDetailPageOfSecretProduct()
    {
        $this->loginTo($this->createCustomer());
        $Product = $this->createProduct('テスト');

        /** @var Client $client */
        $client = $this->client;
        $client->request('POST',
            $this->app->url('product_detail', array('id' => $Product->getId()))
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testGuestCanSeeDetailPageOfPublicProduct()
    {
        $Product = $this->createProduct('【秘密の商品】テスト');

        /** @var Client $client */
        $client = $this->client;
        $client->request('POST',
            $this->app->url('product_detail', array('id' => $Product->getId()))
        );
        $this->assertFalse($client->getResponse()->isSuccessful());
    }

    public function testCustomerCanSeeDetailPageOfPublicProduct()
    {
        $this->loginTo($this->createCustomer());
        $Product = $this->createProduct('【秘密の商品】テスト');

        /** @var Client $client */
        $client = $this->client;
        $client->request('POST',
            $this->app->url('product_detail', array('id' => $Product->getId()))
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testGuestCannotSeeSecretProductInProductList()
    {
        $this->createProduct('【秘密の商品】テスト');
        $client = $this->client;
        $crawler = $client->request('GET', $this->app->url('product_list'));
        $this->assertNotContains('【秘密の商品】', $crawler->html());
    }

    public function testCustomerCannotSeeSecretProductInProductList()
    {
        $this->loginTo($this->createCustomer());
        $this->createProduct('【秘密の商品】テスト');
        $client = $this->client;
        $crawler = $client->request('GET', $this->app->url('product_list'));
        $this->assertNotContains('【秘密の商品】', $crawler->html());
    }

    public function testAdminCanSeeSecretProductInAdminProductList()
    {
        $this->loginTo($this->createMember());
        $Product = $this->createProduct('【秘密の商品】テスト');
        $client = $this->client;
        $crawler = $client->request('POST',
            $this->app->url('admin_product'),
            array('admin_search_product' => array(
                '_token' => 'dummy',
                'id' => $Product->getId()))
        );
        $this->assertContains('【秘密の商品】テスト', $crawler->html());
    }
}