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

namespace Eccube\Tests\Web;

use Eccube\Common\Constant;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Repository\BaseInfoRepository;

class EntryControllerTest extends AbstractWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->client->enableProfiler();
    }

    protected function createFormData()
    {
        $faker = $this->getFaker();
        $email = $faker->safeEmail;
        $password = $faker->lexify('????????');
        $birth = $faker->dateTimeBetween;

        $form = [
            'name' => [
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ],
            'kana' => [
                'kana01' => $faker->lastKanaName,
                'kana02' => $faker->firstKanaName,
            ],
            'company_name' => $faker->company,
            'postal_code' => $faker->postcode,
            'address' => [
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ],
            'phone_number' => $faker->phoneNumber,
            'email' => [
                'first' => $email,
                'second' => $email,
            ],
            'password' => [
                'first' => $password,
                'second' => $password,
            ],
            'birth' => [
                'year' => $birth->format('Y'),
                'month' => $birth->format('n'),
                'day' => $birth->format('j'),
            ],
            'sex' => 1,
            'job' => 1,
            'point' => 1,
            'user_policy_check' => 1,
            Constant::TOKEN_NAME => 'dummy',
        ];

        return $form;
    }

    public function testRoutingIndex()
    {
        $client = $this->client;
        $crawler = $client->request('GET', $this->generateUrl('entry'));

        $this->expected = '新規会員登録';
        $this->actual = $crawler->filter('.ec-pageHeader > h1')->text();
        $this->verify();

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testConfirm()
    {
        $crawler = $this->client->request('POST',
            $this->generateUrl('entry'),
            [
                'entry' => $this->createFormData(),
                'mode' => 'confirm',
            ]
        );

        $this->expected = '新規会員登録確認';
        $this->actual = $crawler->filter('.ec-pageHeader > h1')->text();
        $this->verify();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testConfirmWithError()
    {
        $crawler = $this->client->request('POST',
            $this->generateUrl('entry'),
            [
                'entry' => [
                    Constant::TOKEN_NAME => 'dummy',
                ],
                'mode' => 'confirm',
            ]
        );

        $this->expected = '新規会員登録';
        $this->actual = $crawler->filter('.ec-pageHeader > h1')->text();
        $this->verify();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testConfirmWithModeNotFound()
    {
        $client = $this->client;

        $crawler = $client->request('POST',
            $this->generateUrl('entry'),
            [
                'entry' => $this->createFormData(),
                'mode' => 'aaaaa',
            ]
        );

        $this->expected = '新規会員登録';
        $this->actual = $crawler->filter('.ec-pageHeader > h1')->text();
        $this->verify();

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testCompleteWithActivate()
    {
        $BaseInfo = $this->container->get(BaseInfoRepository::class)->get();
        $BaseInfo->setOptionCustomerActivate(1);
        $this->entityManager->flush();

        $client = $this->client;
        $crawler = $client->request('POST',
            $this->generateUrl('entry'),
            [
                'entry' => $this->createFormData(),
                'mode' => 'complete',
            ]
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('entry_complete')));

        $collectedMessages = $this->getMailCollector(false)->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = '['.$BaseInfo->getShopName().'] 会員登録のご確認';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testRoutingComplete()
    {
        $client = $this->client;
        $client->request('GET', $this->generateUrl('entry_complete'));

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testActivate()
    {
        $BaseInfo = $this->container->get(BaseInfoRepository::class)->get();
        $Customer = $this->createCustomer();
        $secret_key = $Customer->getSecretKey();
        $Status = $this->entityManager->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::NONACTIVE);
        $Customer->setStatus($Status);
        $this->entityManager->flush();

        $client = $this->client;
        $client->request('GET', $this->generateUrl('entry_activate', ['secret_key' => $secret_key]));

        $this->assertTrue($client->getResponse()->isSuccessful());
        $collectedMessages = $this->getMailCollector(false)->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];
        $this->expected = '['.$BaseInfo->getShopName().'] 会員登録が完了しました。';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testActivateWithNotFound()
    {
        $this->client->request('GET', $this->generateUrl('entry_activate', ['secret_key' => 'aaaaa']));
        $this->expected = 404;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }

    public function testActivateWithAbort()
    {
        $this->client->request('GET', $this->generateUrl('entry_activate', ['secret_key' => '+++++++']));
        $this->expected = 403;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }
}
