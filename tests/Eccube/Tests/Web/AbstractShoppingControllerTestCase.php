<?php

namespace Eccube\Tests\Web;

/**
 * ShoppingController 用 WebTest の抽象クラス.
 *
 * ShoppingController の WebTest をする場合に汎用的に使用する.
 *
 * @author Kentaro Ohkouchi
 */
abstract class AbstractShoppingControllerTestCase extends AbstractWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->initializeMailCatcher();
    }

    public function tearDown()
    {
        $this->cleanUpMailCatcherMessages();
        parent::tearDown();
    }

    public function createShippingFormData()
    {
        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);

        $email = $faker->safeEmail;

        $form = array(
            'name' => array(
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ),
            'kana' => array(
                'kana01' => $faker->lastKanaName ,
                'kana02' => $faker->firstKanaName,
            ),
            'company_name' => $faker->company,
            'zip' => array(
                'zip01' => $faker->postcode1(),
                'zip02' => $faker->postcode2(),
            ),
            'address' => array(
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ),
            'tel' => array(
                'tel01' => $tel[0],
                'tel02' => $tel[1],
                'tel03' => $tel[2],
            ),
            '_token' => 'dummy'
        );
        return $form;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Client $client
     * @param int $product_class_id
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function scenarioCartIn($client, $product_class_id = 1)
    {
        /** @var \Eccube\Entity\ProductClass $ProductClass */
        $ProductClass = $this->app['eccube.repository.product_class']->find($product_class_id);

        $crawler = $client->request(
            'POST',
            $this->app->path('product_detail', ['id' => $ProductClass->getProduct()->getId()]),
            [
                'mode' => 'add_cart',
                'ProductClass' => $ProductClass->getId(),
                'quantity' => 1,
                '_token' => 'dummy',
            ]
        );
        $this->app['eccube.service.cart']->lock();

        return $crawler;
    }

    protected function scenarioInput($client, $formData)
    {
        $crawler = $client->request(
            'POST',
            $this->app->path('shopping_nonmember'),
            array('nonmember' => $formData)
        );
        $this->app['eccube.service.cart']->lock();
        return $crawler;
    }

    protected function scenarioConfirm($client)
    {
        $crawler = $client->request('GET', $this->app->path('shopping'));
        return $crawler;
    }

    protected function scenarioComplete($client, $confirm_url, array $shippings = array())
    {
        $faker = $this->getFaker();
        if (count($shippings) < 1) {
            $shippings = array(
                array(
                    'Delivery' => 1,
                    'DeliveryTime' => 1
                ),
            );
        }

        $crawler = $client->request(
            'POST',
            $confirm_url,
            array('_shopping_order' =>
                  array(
                      'Shippings' => $shippings,
                      'Payment' => 3,
                      'message' => $faker->text(),
                      '_token' => 'dummy'
                  )
            )
        );

        return $crawler;
    }
}
