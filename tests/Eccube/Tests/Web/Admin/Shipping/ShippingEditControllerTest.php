<?php

namespace Eccube\Tests\Web\Admin\Shipping;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Faker\Generator;
use Eccube\Entity\Master\ShippingStatus;
use Eccube\Repository\ShippingRepository;
use Eccube\Common\Constant;
use Eccube\Entity\Shipping;

class ShippingEditControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var ShippingRepository
     */
    protected $shippingRepository;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->shippingRepository = $this->container->get(ShippingRepository::class);
    }

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
        $arrFormData['ShippingStatus'] = ShippingStatus::PREPARED;
        $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_new'),
            array(
                'shipping' => $arrFormData,
            )
        );

        $crawler = $this->client->followRedirect();

        $success = $crawler->filter('#page_admin_shipping_edit > div.c-container > div.c-contentsArea > div.alert.alert-success')->text();
        $this->assertContains('出荷情報を登録しました。', $success);
    }


    public function testEditShippingStatusShipped()
    {
        $this->client->enableProfiler();

        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();
        $Shipping->setShippingStatus($this->entityManager->find(ShippingStatus::class, ShippingStatus::PREPARED));
        $this->entityManager->persist($Shipping);
        $this->entityManager->flush();

        $this->assertNull($Shipping->getShippingDate());
        $arrFormData = $this->createShippingForm();
        $arrFormData['ShippingStatus'] = ShippingStatus::SHIPPED;
        $arrFormData['notify_email'] = 'on';
        $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_edit', ['id' => $Shipping->getId()]),
            array(
                'shipping' => $arrFormData
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());


        $Messages = $this->getMailCollector(false)->getMessages();
        self::assertEquals(1, count($Messages));
        /** @var \Swift_Message $Message */
        $Message = $Messages[0];

        self::assertRegExp('/\[.*?\] 商品出荷のお知らせ/', $Message->getSubject());
        self::assertEquals([$Order->getEmail() => null], $Message->getTo());

        $crawler = $this->client->followRedirect();

        $success = $crawler->filter('#page_admin_shipping_edit > div.c-container > div.c-contentsArea > div.alert.alert-success')->text();
        $this->assertContains('出荷情報を登録しました。', $success);

        $this->assertNotNull($Shipping->getShippingDate());
    }

    public function testEditShippingStatusPrepared()
    {
        $Order = $this->createOrder($this->createCustomer());
        /** @var Shipping $Shipping */
        $Shipping = $Order->getShippings()->first();
        $Shipping->setShippingStatus($this->entityManager->find(ShippingStatus::class, ShippingStatus::SHIPPED));
        $Shipping->setShippingDate(new \DateTime());
        $this->entityManager->persist($Shipping);
        $this->entityManager->flush();

        $this->assertNotNull($Shipping->getShippingDate());
        $arrFormData = $this->createShippingForm();
        $arrFormData['ShippingStatus'] = ShippingStatus::PREPARED;
        $this->client->request(
            'POST',
            $this->generateUrl('admin_shipping_edit', ['id' => $Shipping->getId()]),
            array(
                'shipping' => $arrFormData
            )
        );
        $crawler = $this->client->followRedirect();

        $success = $crawler->filter('#page_admin_shipping_edit > div.c-container > div.c-contentsArea > div.alert.alert-success')->text();
        $this->assertContains('出荷情報を登録しました。', $success);

        $this->assertNull($Shipping->getShippingDate());
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
            Constant::TOKEN_NAME => 'dummy'
        );
        return $arrFormData;
    }
}
