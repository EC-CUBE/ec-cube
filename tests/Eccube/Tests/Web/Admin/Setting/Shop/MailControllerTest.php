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

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Entity\MailTemplate;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * Class MailControllerTest
 */
class MailControllerTest extends AbstractAdminWebTestCase
{
    public function tearDown()
    {
        $themeDir = self::$container->getParameter('eccube_theme_front_dir');
        if (file_exists($themeDir.'/Mail/order.twig')) {
            unlink($themeDir.'/Mail/order.twig');
        }
        if (file_exists($themeDir.'/Mail/order.html.twig')) {
            unlink($themeDir.'/Mail/order.html.twig');
        }
        parent::tearDown();
    }

    /**
     * @return mixed
     */
    public function createMail()
    {
        $faker = $this->getFaker();
        // create new mail
        $Mail = new MailTemplate();
        $Mail->setName($faker->word);
        $Mail->setFileName('Mail/order.twig');
        $Mail->setMailSubject($faker->word);
        $this->entityManager->persist($Mail);
        $this->entityManager->flush();

        return $Mail;
    }

    /**
     * Routing
     */
    public function testRouting()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_shop_mail'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Edit
     */
    public function testRoutingEdit()
    {
        $MailTemplate = $this->createMail();
        $this->client->request('GET',
            $this->generateUrl('admin_setting_shop_mail_edit', ['id' => $MailTemplate->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Edit
     */
    public function testEdit()
    {
        $MailTemplate = $this->createMail();
        $form = [
            '_token' => 'dummy',
            'template' => $MailTemplate->getId(),
            'mail_subject' => 'Test Subject',
            'tpl_data' => 'Test TPL Data',
        ];
        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_mail_edit', ['id' => $MailTemplate->getId()]),
            ['mail' => $form]
        );

        $redirectUrl = $this->generateUrl('admin_setting_shop_mail_edit', ['id' => $MailTemplate->getId()]);
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = $form['mail_subject'];
        $this->expected = $MailTemplate->getMailSubject();
        $this->verify();
    }

    /**
     * Edit Html
     */
    public function testEditHtml()
    {
        $MailTemplate = $this->createMail();
        $form = [
            '_token' => 'dummy',
            'template' => $MailTemplate->getId(),
            'mail_subject' => 'Test Subject',
            'tpl_data' => 'Test TPL Data',
            'html_tpl_data' => '<font color="red">Test HTML TPL Data</font>',
        ];
        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_mail_edit', ['id' => $MailTemplate->getId()]),
            ['mail' => $form]
        );

        $redirectUrl = $this->generateUrl('admin_setting_shop_mail_edit', ['id' => $MailTemplate->getId()]);
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->actual = $form['mail_subject'];
        $this->expected = $MailTemplate->getMailSubject();
        $this->verify();
    }

    public function testEditFail()
    {
        $mid = 99999;
        $form = [
            '_token' => 'dummy',
            'template' => $mid,
            'mail_subject' => 'Test Subject',
        ];
        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_mail_edit', ['id' => $mid]),
            ['mail' => $form]
        );

        $redirectUrl = $this->generateUrl('admin_setting_shop_mail');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $outPut = self::$container->get('session')->getFlashBag()->get('eccube.admin.error');
        $this->actual = array_shift($outPut);
        $this->expected = 'admin.common.save_error';
        $this->verify();
    }

    /**
     * Create
     */
    public function testCreateFail()
    {
        $form = [
            '_token' => 'dummy',
            'template' => null,
            'mail_subject' => null,
        ];
        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_mail'),
            ['mail' => $form]
        );

        $redirectUrl = $this->generateUrl('admin_setting_shop_mail');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }
}
