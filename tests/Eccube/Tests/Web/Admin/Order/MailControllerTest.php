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

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\MailHistory;
use Eccube\Entity\MailTemplate;
use Eccube\Entity\Order;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class MailControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var Customer
     */
    protected $Customer;

    /**
     * @var Order
     */
    protected $Order;

    public function setUp()
    {
        parent::setUp();
        $faker = $this->getFaker();
        $this->Member = $this->createMember();
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);

        $MailTemplate = new MailTemplate();
        $MailTemplate
            ->setName($faker->word)
            ->setMailSubject($faker->word)
            ->setCreator($this->Member);
        $this->entityManager->persist($MailTemplate);
        $this->entityManager->flush();
        for ($i = 0; $i < 3; $i++) {
            $this->MailHistories[$i] = new MailHistory();
            $this->MailHistories[$i]
                ->setOrder($this->Order)
                ->setSendDate(new \DateTime())
                ->setMailBody($faker->realText())
                ->setCreator($this->Member)
                ->setMailSubject('mail_subject-'.$i);

            $this->entityManager->persist($this->MailHistories[$i]);
            $this->entityManager->flush();
        }
    }

    public function createFormData()
    {
        $faker = $this->getFaker();
        $form = [
            'template' => 1,
            'mail_subject' => $faker->word,
            '_token' => 'dummy',
        ];

        return $form;
    }

    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_order_mail', ['id' => $this->Order->getId()])
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexWithConfirm()
    {
        $form = $this->createFormData();
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_mail', ['id' => $this->Order->getId()]),
            [
                'mail' => $form,
                'mode' => 'confirm',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testComplete()
    {
        $this->client->enableProfiler();
        $form = $this->createFormData();
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_mail', ['id' => $this->Order->getId()]),
            [
                'admin_order_mail' => $form,
                'mode' => 'complete',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_edit', ['id' => $this->Order->getId()])));

        $mailCollector = $this->getMailCollector(false);
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $this->expected = '['.$BaseInfo->getShopName().'] '.$form['mail_subject'];
        $this->actual = $Message->getSubject();
        $this->verify();
    }
}
