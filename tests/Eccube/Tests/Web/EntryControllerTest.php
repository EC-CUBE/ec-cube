<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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
        $tel = explode('-', $faker->phoneNumber);

        $email = $faker->safeEmail;
        $password = $faker->lexify('????????');
        $birth = $faker->dateTimeBetween;

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
            'fax' => array(
                'fax01' => $tel[0],
                'fax02' => $tel[1],
                'fax03' => $tel[2],
            ),
            'email' => array(
                'first' => $email,
                'second' => $email,
            ),
            'password' => array(
                'first' => $password,
                'second' => $password,
            ),
            'birth' => array(
                'year' => $birth->format('Y'),
                'month' => $birth->format('n'),
                'day' => $birth->format('j'),
            ),
            'sex' => 1,
            'job' => 1,
            'point' => 1,
            Constant::TOKEN_NAME => $this->getCsrfToken('entry')
        );
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
            array(
                'entry' => $this->createFormData(),
                'mode' => 'confirm',
            )
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
            array(
                'entry' => array(
                    Constant::TOKEN_NAME => $this->getCsrfToken('entry')
                ),
                'mode' => 'confirm'
            )
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
            array(
                'entry' => $this->createFormData(),
                'mode' => 'aaaaa'
            )
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
            array(
                'entry' => $this->createFormData(),
                'mode' => 'complete'
            )
        );

        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('entry_complete')));

        $collectedMessages = $this->getMailCollector(false)->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $this->expected = '[' . $BaseInfo->getShopName() . '] 会員登録のご確認';
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
        $client->request('GET', $this->generateUrl('entry_activate', array('secret_key' => $secret_key)));

        $this->assertTrue($client->getResponse()->isSuccessful());
        $collectedMessages = $this->getMailCollector(false)->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];
        $this->expected = '[' . $BaseInfo->getShopName() . '] 会員登録が完了しました。';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testActivateWithNotFound()
    {
        $this->client->request('GET', $this->generateUrl('entry_activate', array('secret_key' => 'aaaaa')));
        $this->expected = 404;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();

    }

    public function testActivateWithAbort()
    {
        $this->client->request('GET', $this->generateUrl('entry_activate', array('secret_key' => '+++++++')));
        $this->expected = 403;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
    }
}
