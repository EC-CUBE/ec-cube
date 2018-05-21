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
            ->setMailHeader($faker->word)
            ->setMailFooter($faker->word)
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
            'mail_header' => $faker->paragraph,
            'mail_footer' => $faker->paragraph,
            'tpl_data' => $faker->text,
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

    public function testIndexWithComplete()
    {
        $this->client->enableProfiler();
        $form = $this->createFormData();
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_mail', ['id' => $this->Order->getId()]),
            [
                'mail' => $form,
                'mode' => 'complete',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_page', ['page_no' => 1])));

        $mailCollector = $this->getMailCollector(false);
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $Message */
        $Message = $collectedMessages[0];

        $BaseInfo = $this->container->get(BaseInfo::class);
        $this->expected = '['.$BaseInfo->getShopName().'] '.$form['mail_subject'];
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testView()
    {
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_mail_view'),
            [
                'id' => $this->MailHistories[0]->getId(),
            ],
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testMailAll()
    {
        $form = $this->createFormData();
        $crawler = $this->client->request(
            'GET',
            $this->generateUrl('admin_order_mail_all')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testMailAllWithComplete()
    {
        $this->client->enableProfiler();

        $ids = [];
        for ($i = 0; $i < 5; $i++) {
            $Order = $this->createOrder($this->Customer);
            $ids[] = $Order->getId();
        }

        $form = $this->createFormData();
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl('admin_order_mail_all'),
            [
                'mail' => $form,
                'mode' => 'complete',
                'ids' => implode(',', $ids),
            ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('admin_order_page', ['page_no' => 1])));

        $mailCollector = $this->getMailCollector(false);
        $this->assertEquals(5, $mailCollector->getMessageCount());

        $Messages = $mailCollector->getMessages();
        $Message = $Messages[0];

        $BaseInfo = $this->container->get(BaseInfo::class);
        $this->expected = '['.$BaseInfo->getShopName().'] '.$form['mail_subject'];
        $this->actual = $Message->getSubject();
        $this->verify();
    }
}
