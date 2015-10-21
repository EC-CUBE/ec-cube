<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Entity\MailHistory;
use Eccube\Entity\MailTemplate;

/**
 * MailHistoryRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class MailHistoryRepositoryTest extends EccubeTestCase
{
    protected $Customer;
    protected $Order;
    protected $MailHistories;

    public function setUp()
    {
        parent::setUp();
        $faker = $this->getFaker();
        $this->Member = $this->app['eccube.repository.member']->find(2);
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $MailTemplate = new MailTemplate();
        $MailTemplate
            ->setName($faker->word)
            ->setHeader($faker->word)
            ->setFooter($faker->word)
            ->setSubject($faker->word)
            ->setCreator($this->Member)
            ->setDelFlg(Constant::DISABLED);
        $this->app['orm.em']->persist($MailTemplate);
        $this->app['orm.em']->flush();
        for ($i = 0; $i < 3; $i++) {
            $this->MailHistories[$i] = new MailHistory();
            $this->MailHistories[$i]
                ->setMailTemplate($MailTemplate)
                ->setOrder($this->Order)
                ->setSendDate(new \DateTime())
                ->setMailBody($faker->realText())
                ->setCreator($this->Member)
                ->setSubject('subject-'.$i);

            $this->app['orm.em']->persist($this->MailHistories[$i]);
            $this->app['orm.em']->flush();
        }
    }

    public function testGetByCustomerAndId()
    {
        try {
            $MailHistory = $this->app['eccube.repository.mail_history']->getByCustomerAndId($this->Customer, $this->MailHistories[0]->getId());

            $this->expected = 'subject-0';
            $this->actual = $MailHistory->getSubject();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $this->fail($e->getMessage());
        }
        $this->verify();
    }

    public function testGetByCustomerAndIdWithException()
    {
        try {
            $MailHistory = $this->app['eccube.repository.mail_history']->getByCustomerAndId($this->Customer, 99999);
            $this->fail();
        } catch (\Doctrine\ORM\NoResultException $e) {
            $this->expected = 'No result was found for query although at least one row was expected.';
            $this->actual = $e->getMessage();
        }
        $this->verify();
    }
}
