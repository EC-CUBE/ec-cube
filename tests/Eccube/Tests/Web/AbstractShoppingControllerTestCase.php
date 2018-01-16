<?php

namespace Eccube\Tests\Web;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Service\CartService;

/**
 * ShoppingController 用 WebTest の抽象クラス.
 *
 * ShoppingController の WebTest をする場合に汎用的に使用する.
 *
 * @author Kentaro Ohkouchi
 */
abstract class AbstractShoppingControllerTestCase extends AbstractWebTestCase
{
    protected $token;

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
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

    protected function scenarioCartIn(Customer $Customer = null, $product_class_id = 1)
    {
        $token = $this->getCsrfToken(Constant::TOKEN_NAME);

        if ($Customer) {
            $this->loginTo($Customer);
        }

        $crawler = $this->client->request(
            'PUT',
            $this->generateUrl(
                'cart_handle_item',
                [
                    'operation' => 'up',
                    'productClassId' => $product_class_id,
                ]
            ),
            [Constant::TOKEN_NAME => $token]
        );
        $this->container->get(CartService::class)->lock();
        $this->container->get(CartService::class)->save();

        return $crawler;
    }

    protected function scenarioInput($formData)
    {
        $token = $this->getCsrfToken('nonmember');
        $formData[Constant::TOKEN_NAME] = $token;
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('shopping_nonmember'),
            ['nonmember' => $formData, '_token' => $token]
        );
        $this->container->get(CartService::class)->lock();
        $this->container->get(CartService::class)->save();
        return $crawler;
    }

    protected function scenarioConfirm(Customer $Customer)
    {
        if ($Customer) {
            $this->loginTo($Customer);
        }
        $crawler = $this->client->request('GET', $this->generateUrl('shopping'));
        return $crawler;
    }

    protected function scenarioRedirectTo(Customer $Cusotmer, $parameters)
    {
        if ($Cusotmer) {
            $this->loginTo($Cusotmer);
        }
        return $this->client->request(
            'POST',
            $this->generateUrl('shopping_redirect_to'),
            $parameters
        );
    }

    protected function scenarioComplete(Customer $Customer, $confirm_url, array $shippings = array())
    {
        $token = $this->getCsrfToken('_shopping_order');

        if ($Customer) {
            $this->loginTo($Customer);
        }

        $faker = $this->getFaker();
        if (count($shippings) < 1) {
            $shippings = array(
                array(
                    'Delivery' => 1,
                    'DeliveryTime' => 1
                ),
            );
        }

        $this->client->enableProfiler();

        $crawler = $this->client->request(
            'POST',
            $confirm_url,
            array('_shopping_order' =>
                  array(
                      'Shippings' => $shippings,
                      'Payment' => 3,
                      'message' => $faker->realText(),
                      '_token' => $token
                  )
            )
        );

        return $crawler;
    }
}
