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

namespace Eccube\Tests\Web\Mypage;

use Eccube\Entity\Customer;
use Eccube\Tests\Web\AbstractWebTestCase;

class ChangeControllerTest extends AbstractWebTestCase
{
    /**
     * @var Customer
     */
    protected $Customer;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
    }

    protected function createFormData()
    {
        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);

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
            'zip' => [
                'zip01' => $faker->postcode1(),
                'zip02' => $faker->postcode2(),
            ],
            'address' => [
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ],
            'tel' => [
                'tel01' => $tel[0],
                'tel02' => $tel[1],
                'tel03' => $tel[2],
            ],
            'fax' => [
                'fax01' => $tel[0],
                'fax02' => $tel[1],
                'fax03' => $tel[2],
            ],
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
            'point' => 10,
            '_token' => 'dummy',
        ];

        return $form;
    }

    public function testIndex()
    {
        $this->loginTo($this->Customer);

        $this->client->request(
            'GET',
            $this->generateUrl('mypage_change')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexWithPost()
    {
        $this->loginTo($this->Customer);

        $form = $this->createFormData();
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('mypage_change'),
            ['entry' => $form]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('mypage_change_complete')));

        $this->expected = $form['email']['first'];
        $this->actual = $this->Customer->getEmail();
        $this->verify();
    }

    public function testIndexWithPostDefaultPassword()
    {
        $this->loginTo($this->Customer);

        $form = $this->createFormData();
        $form['password'] = [
            'first' => $this->eccubeConfig['eccube_default_password'],
            'second' => $this->eccubeConfig['eccube_default_password'],
        ];
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('mypage_change'),
            ['entry' => $form]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('mypage_change_complete')));
    }

    public function testIndexWithPostInvalid()
    {
        $this->loginTo($this->Customer);

        $this->client->request(
            'POST',
            $this->generateUrl('mypage_change'),
            []
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testComplete()
    {
        $this->loginTo($this->Customer);

        $this->client->request(
            'GET',
            $this->generateUrl('mypage_change_complete')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}
