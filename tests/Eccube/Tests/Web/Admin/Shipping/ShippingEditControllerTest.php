<?php

namespace Eccube\Tests\Web\Admin\Shipping;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Faker\Generator;

class ShippingEditControllerTest extends AbstractAdminWebTestCase
{
    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_shipping_new')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testNewShippingEmptyShipment()
    {
        $arrFormData = $this->createShippingForm();

        $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_new'),
            array(
                'shipping' => $arrFormData,
                'mode' => 'register'
            )
        );

        $crawler = $this->client->followRedirect();

        $success = $crawler->filter('#page_admin_shipping_edit > div.c-container > div.c-contentsArea > div.alert.alert-success')->text();
        $this->assertContains('出荷情報を登録しました。', $success);
    }

    /**
     * @return array
     */
    private function createShippingForm(): array
    {
        /** @var Generator $faker */
        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);

        $arrFormData = array(
            'name' => array(
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ),
            'kana' => array(
                'kana01' => $faker->lastKanaName,
                'kana02' => $faker->firstKanaName,
            ),
            'company_name' => $faker->company,
            'zip' => array(
                'zip01' => $faker->postcode1(),
                'zip02' => $faker->postcode2(),
            ),
            'address' => array(
                'pref' => $faker->numberBetween(1, 47),
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ),
            'tel' => array(
                'tel01' => $tel[0],
                'tel02' => $tel[1],
                'tel03' => $tel[2],
            ),
            'fax' => array(
                'fax01' => $tel[0],
                'fax02' => $tel[1],
                'fax03' => $tel[2],
            ),
            'Delivery' => 1,
            '_token' => 'dummy'
        );
        return $arrFormData;
    }
}
