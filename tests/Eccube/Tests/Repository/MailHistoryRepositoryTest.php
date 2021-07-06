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

namespace Eccube\Tests\Repository;

use Eccube\Entity\Customer;
use Eccube\Entity\MailHistory;
use Eccube\Entity\MailTemplate;
use Eccube\Entity\Member;
use Eccube\Entity\Order;
use Eccube\Repository\MailHistoryRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * MailHistoryRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class MailHistoryRepositoryTest extends EccubeTestCase
{
    /**
     * @var Member
     */
    protected $Member;

    /**
     * @var Customer
     */
    protected $Customer;

    /**
     * @var Order
     */
    protected $Order;

    /**
     * @var MailHistory[]
     */
    protected $MailHistories;

    /**
     * @var MailHistoryRepository
     */
    protected $mailHistoryRepo;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $faker = $this->getFaker();
        $this->mailHistoryRepo = $this->entityManager->getRepository(\Eccube\Entity\MailHistory::class);

        $this->Member = $this->entityManager->getRepository(\Eccube\Entity\Member::class)->find(2);
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
                ->setMailHtmlBody($faker->realText())
                ->setCreator($this->Member)
                ->setMailSubject('mail_subject-'.$i);

            $this->entityManager->persist($this->MailHistories[$i]);
            $this->entityManager->flush();
        }
    }

    public function testGetByCustomerAndId()
    {
        try {
            $MailHistory = $this->mailHistoryRepo->getByCustomerAndId($this->Customer, $this->MailHistories[0]->getId());

            $this->expected = 'mail_subject-0';
            $this->actual = $MailHistory->getMailSubject();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $this->fail($e->getMessage());
        }
        $this->verify();
    }

    public function testGetByCustomerAndIdWithException()
    {
        try {
            $this->mailHistoryRepo->getByCustomerAndId($this->Customer, 99999);
            $this->fail();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $this->expected = 'No result was found for query although at least one row was expected.';
            $this->actual = $e->getMessage();
        }
        $this->verify();
    }
}
