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

namespace Eccube\Service;

use Eccube\Application;

class MailService
{
    /** @var \Eccube\Application */
    public $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }


    /**
     * Send customer confirm mail.
     *
     * @param $Customer 会員情報
     * @param $activateUrl アクティベート用url
     */
    public function sendCustomerConfirmMail(\Eccube\Entity\Customer $Customer, $activateUrl)
    {

        $body = $this->app->renderView('Mail/entry_confirm.twig', array(
            'Customer' => $Customer,
            'activateUrl' => $activateUrl,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[EC-CUBE3] 会員登録のご確認')
            ->setFrom(array('sample@example.com'))
            ->setTo(array($Customer->getEmail()))
            ->setBcc($this->app['config']['mail_cc'])
            ->setBody($body);

        $this->app->mail($message);

    }


    /**
     * Send customer complete mail.
     *
     * @param $Customer 会員情報
     */
    public function sendCustomerCompleteMail(\Eccube\Entity\Customer $Customer)
    {

        $body = $this->app->renderView('Mail/entry_complete.twig', array(
            'Customer' => $Customer,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[EC-CUBE3] 会員登録が完了しました。')
            ->setFrom(array('sample@example.com'))
            ->setTo(array($Customer->getEmail()))
            ->setBcc($this->app['config']['mail_cc'])
            ->setBody($body);

        $this->app->mail($message);
    }



    /**
     * Send withdraw mail.
     *
     * @param $Customer 会員情報
     * @param $BaseInfo baseinfo
     * @param $email 会員email
     */
    public function sendCustomerWithdrawMail(\Eccube\Entity\Customer $Customer, \Eccube\Entity\BaseInfo $BaseInfo, $email)
    {

        $body = $this->app->renderView('Mail/customer_withdraw_mail.twig', array(
            'Customer' => $Customer,
            'BaseInfo' => $BaseInfo,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[EC-CUBE3] 退会手続きのご完了')
            ->setFrom(array('sample@example.com'))
            ->setTo(array($email))
            ->setBcc($this->app['config']['mail_cc'])
            ->setBody($body);

        $this->app->mail($message);

    }


    /**
     * Send contact mail.
     *
     * @param $formData お問い合わせ内容
     */
    public function sendrContactMail($formData)
    {

        // 問い合わせ者にメール送信
        $message = \Swift_Message::newInstance()
            ->setSubject('[EC-CUBE3] お問い合わせを受け付けました。')
            ->setFrom(array('sample@example.com'))
            ->setTo(array($formData['email']))
            ->setBcc($this->app['config']['mail_cc'])
            ->setBody($formData['contents']);

        $this->app->mail($message);

        // 管理者へメール送信
        $message = \Swift_Message::newInstance()
            ->setSubject('[EC-CUBE3] お問い合わせがあります。')
            ->setFrom(array('sample@example.com'))
            ->setTo($this->app['config']['mail_cc'])
            ->setBody($formData['contents']);

        $this->app->mail($message);

    }


    /**
     * Send order mail.
     *
     * @param $Order 受注情報
     */
    public function sendOrderMail(\Eccube\Entity\Order $Order)
    {

        $body = $this->app->renderView('Mail/order.twig', array(
            'Order' => $Order,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[EC-CUBE3] 購入が完了しました。')
            ->setFrom(array('sample@example.com'))
            ->setTo(array($Order->getEmail()))
            ->setBcc($this->app['config']['mail_cc'])
            ->setBody($body);

        $this->app->mail($message);

    }


    /**
     * Send admin customer confirm mail.
     *
     * @param $Customer 会員情報
     * @param $BaseInfo baseinfo
     * @param $activateUrl アクティベート用url
     */
    public function sendAdminCustomerConfirmMail(\Eccube\Entity\Customer $Customer, \Eccube\Entity\BaseInfo $BaseInfo, $activateUrl)
    {

        $body = $this->app->renderView('Mail/entry_confirm.twig', array(
            'Customer' => $Customer,
            'activateUrl' => $activateUrl,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('会員登録のご確認')
            ->setFrom(array($BaseInfo->getEmail03() => $BaseInfo->getShopName()))
            ->setTo(array($Customer->getEmail()))
            ->setBcc($BaseInfo->getEmail01())
            ->setReplyTo($BaseInfo->getEmail03())
            ->setReturnPath($BaseInfo->getEmail04())
            ->setBody($body);

        $this->app->mail($message);

    }


    /**
     * Send admin order mail.
     *
     * @param $Order 受注情報
     * @param $formData 入力内容
     */
    public function sendAdminOrderMail(\Eccube\Entity\Order $Order, $formData)
    {

        $body = $this->app->renderView('Mail/order.twig', array(
            'header' => $formData['header'],
            'footer' => $formData['footer'],
            'Order' => $Order,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject($formData['subject'])
            ->setFrom(array('sample@example.com'))
            ->setTo(array($Order->getEmail()))
            ->setBcc($this->app['config']['mail_cc'])
            ->setBody($body);

        $this->app->mail($message);

    }

    /**
     * Send password reset notification mail.
     *
     * @param $Customer 会員情報
     */
    public function sendPasswordResetNotificationMail(\Eccube\Entity\Customer $Customer, $reset_url)
    {
        $body = $this->app['view']->render('Mail/forgot_mail.twig', array(
            'Customer' => $Customer,
            'reset_url' => $reset_url
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[EC-CUBE3] パスワード変更の確認')
            ->setFrom(array('sample@example.com'))
            ->setTo(array($Customer->getEmail()))
            ->setBcc($this->app['config']['mail_cc'])
            ->setBody($body);

        $this->app->mail($message);
    }

    /**
     * Send password reset notification mail.
     *
     * @param $Customer 会員情報
     */
    public function sendPasswordResetCompleteMail(\Eccube\Entity\Customer $Customer, $password)
    {
        $body = $this->app['view']->render('Mail/reset_complete_mail.twig', array(
                        'Customer' => $Customer,
                        'password' => $password,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[EC-CUBE3] パスワード変更のお知らせ')
            ->setFrom(array('sample@example.com'))
            ->setTo(array($Customer->getEmail()))
            ->setBcc($this->app['config']['mail_cc'])
            ->setBody($body);

        $this->app->mail($message);
    }


}
