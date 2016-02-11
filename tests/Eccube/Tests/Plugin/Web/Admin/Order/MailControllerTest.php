<?php

namespace Eccube\Tests\Plugin\Web\Admin\Order;

use Eccube\Common\Constant;
use Eccube\Entity\MailHistory;
use Eccube\Entity\MailTemplate;
use Eccube\Event\EccubeEvents;
use Eccube\Tests\Plugin\Web\Admin\AbstractAdminWebTestCase;

class MailControllerTest extends AbstractAdminWebTestCase
{
    protected $Customer;
    protected $Order;

    public function setUp()
    {
        parent::setUp();
        $this->initializeMailCatcher();
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

    public function tearDown()
    {
        $this->cleanUpMailCatcherMessages();
        parent::tearDown();
    }

    public function createFormData()
    {
        $faker = $this->getFaker();
        $form = array(
            'template' => 1,
            'subject' => $faker->word,
            'header' => $faker->paragraph,
            'footer' => $faker->paragraph,
            '_token' => 'dummy'
        );
        return $form;
    }

    public function testIndex()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_order_mail', array('id' => $this->Order->getId()))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $expected = array(
            EccubeEvents::ADMIN_ORDER_MAIL_INDEX_INITIALIZE,
        );

        $this->verifyOutputString($expected);
    }

    public function testIndexWithConfirm()
    {
        $form = $this->createFormData();
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_mail', array('id' => $this->Order->getId())),
            array(
                'mail' => $form,
                'mode' => 'confirm'
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $expected = array(
            EccubeEvents::ADMIN_ORDER_MAIL_INDEX_INITIALIZE,
            EccubeEvents::ADMIN_ORDER_MAIL_INDEX_CONFIRM,
        );

        $this->verifyOutputString($expected);
    }

    public function testIndexWithComplete()
    {
        $form = $this->createFormData();
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_mail', array('id' => $this->Order->getId())),
            array(
                'mail' => $form,
                'mode' => 'complete'
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order_mail_complete')));

//        $Messages = $this->getMailCatcherMessages();
//        $Message = $this->getMailCatcherMessage($Messages[0]->id);
//
//        $BaseInfo = $this->app['eccube.repository.base_info']->get();
//        $this->expected = EccubeEvents::MAIL_ADMIN_ORDER;
//        $this->actual = $Message->body;
//        $this->verify();

        $expected = array(
            EccubeEvents::ADMIN_ORDER_MAIL_INDEX_INITIALIZE,
            EccubeEvents::MAIL_ADMIN_ORDER,
            EccubeEvents::ADMIN_ORDER_MAIL_INDEX_COMPLETE,
        );

        $this->verifyOutputString($expected);
    }

    public function testView()
    {
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_mail_view'),
            array(
                'id' => $this->MailHistories[0]->getId()
            ),
            array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $expected = array(
            EccubeEvents::ADMIN_ORDER_MAIL_VIEW_COMPLETE,
        );

        $this->verifyOutputString($expected);
    }

    public function testMailAll()
    {
        $form = $this->createFormData();
        $crawler = $this->client->request(
            'GET',
            $this->app->url('admin_order_mail_all')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $expected = array(
            EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_INITIALIZE,
        );

        $this->verifyOutputString($expected);
    }

    public function testMailAllWithConfirm()
    {
        $ids = array();
        for ($i = 0; $i < 5; $i++) {
            $Order = $this->createOrder($this->Customer);
            $ids[] = $Order->getId();
        }

        $form = $this->createFormData();
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_mail_all'),
            array(
                'mail' => $form,
                'mode' => 'confirm',
                'ids' => implode(',', $ids)
            )
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $expected = array(
            EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_INITIALIZE,
            EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_CONFIRM,
        );

        $this->verifyOutputString($expected);
    }

    public function testMailAllWithComplete()
    {
        $ids = array();
        for ($i = 0; $i < 5; $i++) {
            $Order = $this->createOrder($this->Customer);
            $ids[] = $Order->getId();
        }

        $form = $this->createFormData();
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_order_mail_all'),
            array(
                'mail' => $form,
                'mode' => 'complete',
                'ids' => implode(',', $ids)
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_order_mail_complete')));

//        $Messages = $this->getMailCatcherMessages();
//
//        $this->expected = 10;
//        $this->actual = count($Messages);
//        $this->verify();
//
//        $Message = $this->getMailCatcherMessage($Messages[0]->id);
//
//        $BaseInfo = $this->app['eccube.repository.base_info']->get();
//        $this->expected = EccubeEvents::MAIL_ADMIN_ORDER;
//        $this->actual = $Message->body;
//        $this->verify();

        $expected = array();
        $expected[] = EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_INITIALIZE;
        for ($i = 0; $i < 5; $i++) {
            $expected[] = EccubeEvents::MAIL_ADMIN_ORDER;
        }
        $expected[] = EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_COMPLETE;

        $this->verifyOutputString($expected);
    }
}
