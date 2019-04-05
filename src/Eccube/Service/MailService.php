<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;

class MailService
{
    /** @var \Eccube\Application */
    public $app;


    /** @var \Eccube\Entity\BaseInfo */
    public $BaseInfo;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->BaseInfo = $app['eccube.repository.base_info']->get();
    }


    /**
     * Send customer confirm mail.
     *
     * @param $Customer 会員情報
     * @param $activateUrl アクティベート用url
     */
    public function sendCustomerConfirmMail(\Eccube\Entity\Customer $Customer, $activateUrl)
    {

        log_info('仮会員登録メール送信開始');

        $body = $this->app->renderView('Mail/entry_confirm.twig', array(
            'Customer' => $Customer,
            'BaseInfo' => $this->BaseInfo,
            'activateUrl' => $activateUrl,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[' . $this->BaseInfo->getShopName() . '] 会員登録のご確認')
            ->setFrom(array($this->BaseInfo->getEmail01() => $this->BaseInfo->getShopName()))
            ->setTo(array($Customer->getEmail()))
            ->setBcc($this->BaseInfo->getEmail01())
            ->setReplyTo($this->BaseInfo->getEmail03())
            ->setReturnPath($this->BaseInfo->getEmail04())
            ->setBody($body);

        $event = new EventArgs(
            array(
                'message' => $message,
                'Customer' => $Customer,
                'BaseInfo' => $this->BaseInfo,
                'activateUrl' => $activateUrl,
            ),
            null
        );
        $this->app['eccube.event.dispatcher']->dispatch(EccubeEvents::MAIL_CUSTOMER_CONFIRM, $event);

        $count = $this->app->mail($message, $failures);

        log_info('仮会員登録メール送信完了', array('count' => $count));

        return $count;
    }

    /**
     * Send customer complete mail.
     *
     * @param $Customer 会員情報
     */
    public function sendCustomerCompleteMail(\Eccube\Entity\Customer $Customer)
    {
        log_info('会員登録完了メール送信開始');

        $body = $this->app->renderView('Mail/entry_complete.twig', array(
            'Customer' => $Customer,
            'BaseInfo' => $this->BaseInfo,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[' . $this->BaseInfo->getShopName() . '] 会員登録が完了しました。')
            ->setFrom(array($this->BaseInfo->getEmail01() => $this->BaseInfo->getShopName()))
            ->setTo(array($Customer->getEmail()))
            ->setBcc($this->BaseInfo->getEmail01())
            ->setReplyTo($this->BaseInfo->getEmail03())
            ->setReturnPath($this->BaseInfo->getEmail04())
            ->setBody($body);

        $event = new EventArgs(
            array(
                'message' => $message,
                'Customer' => $Customer,
                'BaseInfo' => $this->BaseInfo,
            ),
            null
        );
        $this->app['eccube.event.dispatcher']->dispatch(EccubeEvents::MAIL_CUSTOMER_COMPLETE, $event);

        $count = $this->app->mail($message);

        log_info('会員登録完了メール送信完了', array('count' => $count));

        return $count;
    }


    /**
     * Send withdraw mail.
     *
     * @param $Customer 会員情報
     * @param $email 会員email
     */
    public function sendCustomerWithdrawMail(\Eccube\Entity\Customer $Customer, $email)
    {
        log_info('退会手続き完了メール送信開始');

        $body = $this->app->renderView('Mail/customer_withdraw_mail.twig', array(
            'Customer' => $Customer,
            'BaseInfo' => $this->BaseInfo,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[' . $this->BaseInfo->getShopName() . '] 退会手続きのご完了')
            ->setFrom(array($this->BaseInfo->getEmail01() => $this->BaseInfo->getShopName()))
            ->setTo(array($email))
            ->setBcc($this->BaseInfo->getEmail01())
            ->setReplyTo($this->BaseInfo->getEmail03())
            ->setReturnPath($this->BaseInfo->getEmail04())
            ->setBody($body);

        $event = new EventArgs(
            array(
                'message' => $message,
                'Customer' => $Customer,
                'BaseInfo' => $this->BaseInfo,
                'email' => $email,
            ),
            null
        );
        $this->app['eccube.event.dispatcher']->dispatch(EccubeEvents::MAIL_CUSTOMER_WITHDRAW, $event);

        $count = $this->app->mail($message);

        log_info('退会手続き完了メール送信完了', array('count' => $count));

        return $count;
    }


    /**
     * Send contact mail.
     *
     * @param $formData お問い合わせ内容
     */
    public function sendContactMail($formData)
    {
        log_info('お問い合わせ受付メール送信開始');

        $body = $this->app->renderView('Mail/contact_mail.twig', array(
            'data' => $formData,
            'BaseInfo' => $this->BaseInfo,
        ));

        // 問い合わせ者にメール送信
        $message = \Swift_Message::newInstance()
            ->setSubject('[' . $this->BaseInfo->getShopName() . '] お問い合わせを受け付けました。')
            ->setFrom(array($this->BaseInfo->getEmail02() => $this->BaseInfo->getShopName()))
            ->setTo(array($formData['email']))
            ->setBcc($this->BaseInfo->getEmail02())
            ->setReplyTo($this->BaseInfo->getEmail02())
            ->setReturnPath($this->BaseInfo->getEmail04())
            ->setBody($body);

        $event = new EventArgs(
            array(
                'message' => $message,
                'formData' => $formData,
                'BaseInfo' => $this->BaseInfo,
            ),
            null
        );
        $this->app['eccube.event.dispatcher']->dispatch(EccubeEvents::MAIL_CONTACT, $event);

        $count = $this->app->mail($message);

        log_info('お問い合わせ受付メール送信完了', array('count' => $count));

        return $count;
    }

    /**
     * Alias of sendContactMail().
     *
     * @param $formData お問い合わせ内容
     * @see sendContactMail()
     * @deprecated since 3.0.0, to be removed in 3.1
     * @link https://github.com/EC-CUBE/ec-cube/issues/1315
     */
    public function sendrContactMail($formData)
    {
        $this->sendContactMail($formData);
    }

    /**
     * Send order mail.
     *
     * @param \Eccube\Entity\Order $Order 受注情報
     * @return string
     */
    public function sendOrderMail(\Eccube\Entity\Order $Order)
    {
        log_info('受注メール送信開始');

        $MailTemplate = $this->app['eccube.repository.mail_template']->find(1);

        $body = $this->app->renderView($MailTemplate->getFileName(), array(
            'header' => $MailTemplate->getHeader(),
            'footer' => $MailTemplate->getFooter(),
            'Order' => $Order,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[' . $this->BaseInfo->getShopName() . '] ' . $MailTemplate->getSubject())
            ->setFrom(array($this->BaseInfo->getEmail01() => $this->BaseInfo->getShopName()))
            ->setTo(array($Order->getEmail()))
            ->setBcc($this->BaseInfo->getEmail01())
            ->setReplyTo($this->BaseInfo->getEmail03())
            ->setReturnPath($this->BaseInfo->getEmail04())
            ->setBody($body);

        $event = new EventArgs(
            array(
                'message' => $message,
                'Order' => $Order,
                'MailTemplate' => $MailTemplate,
                'BaseInfo' => $this->BaseInfo,
            ),
            null
        );
        $this->app['eccube.event.dispatcher']->dispatch(EccubeEvents::MAIL_ORDER, $event);

        $count = $this->app->mail($message);

        log_info('受注メール送信完了', array('count' => $count));

        return $message;

    }


    /**
     * Send admin customer confirm mail.
     *
     * @param $Customer 会員情報
     * @param $activateUrl アクティベート用url
     */
    public function sendAdminCustomerConfirmMail(\Eccube\Entity\Customer $Customer, $activateUrl)
    {
        log_info('仮会員登録再送メール送信開始');

        $body = $this->app->renderView('Mail/entry_confirm.twig', array(
            'Customer' => $Customer,
            'activateUrl' => $activateUrl,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[' . $this->BaseInfo->getShopName() . '] 会員登録のご確認')
            ->setFrom(array($this->BaseInfo->getEmail03() => $this->BaseInfo->getShopName()))
            ->setTo(array($Customer->getEmail()))
            ->setBcc($this->BaseInfo->getEmail01())
            ->setReplyTo($this->BaseInfo->getEmail03())
            ->setReturnPath($this->BaseInfo->getEmail04())
            ->setBody($body);

        $event = new EventArgs(
            array(
                'message' => $message,
                'Customer' => $Customer,
                'BaseInfo' => $this->BaseInfo,
                'activateUrl' => $activateUrl,
            ),
            null
        );
        $this->app['eccube.event.dispatcher']->dispatch(EccubeEvents::MAIL_ADMIN_CUSTOMER_CONFIRM, $event);

        $count = $this->app->mail($message);

        log_info('仮会員登録再送メール送信完了', array('count' => $count));

        return $count;
    }


    /**
     * Send admin order mail.
     *
     * @param $Order 受注情報
     * @param $formData 入力内容
     */
    public function sendAdminOrderMail(\Eccube\Entity\Order $Order, $formData)
    {
        log_info('受注管理通知メール送信開始');

        $body = $this->app->renderView('Mail/order.twig', array(
            'header' => $formData['header'],
            'footer' => $formData['footer'],
            'Order' => $Order,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[' . $this->BaseInfo->getShopName() . '] ' . $formData['subject'])
            ->setFrom(array($this->BaseInfo->getEmail01() => $this->BaseInfo->getShopName()))
            ->setTo(array($Order->getEmail()))
            ->setBcc($this->BaseInfo->getEmail01())
            ->setReplyTo($this->BaseInfo->getEmail03())
            ->setReturnPath($this->BaseInfo->getEmail04())
            ->setBody($body);

        $event = new EventArgs(
            array(
                'message' => $message,
                'Order' => $Order,
                'formData' => $formData,
                'BaseInfo' => $this->BaseInfo,
            ),
            null
        );
        $this->app['eccube.event.dispatcher']->dispatch(EccubeEvents::MAIL_ADMIN_ORDER, $event);

        $count = $this->app->mail($message);

        log_info('受注管理通知メール送信完了', array('count' => $count));

        return $message;
    }

    /**
     * Send password reset notification mail.
     *
     * @param $Customer 会員情報
     */
    public function sendPasswordResetNotificationMail(\Eccube\Entity\Customer $Customer, $reset_url)
    {
        log_info('パスワード再発行メール送信開始');

        $body = $this->app->renderView('Mail/forgot_mail.twig', array(
            'Customer' => $Customer,
            'reset_url' => $reset_url
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[' . $this->BaseInfo->getShopName() . '] パスワード変更のご確認')
            ->setFrom(array($this->BaseInfo->getEmail01() => $this->BaseInfo->getShopName()))
            ->setTo(array($Customer->getEmail()))
            ->setReplyTo($this->BaseInfo->getEmail03())
            ->setReturnPath($this->BaseInfo->getEmail04())
            ->setBody($body);

        $event = new EventArgs(
            array(
                'message' => $message,
                'Customer' => $Customer,
                'BaseInfo' => $this->BaseInfo,
                'resetUrl' => $reset_url,
            ),
            null
        );
        $this->app['eccube.event.dispatcher']->dispatch(EccubeEvents::MAIL_PASSWORD_RESET, $event);

        $count = $this->app->mail($message);

        log_info('パスワード再発行メール送信完了', array('count' => $count));

        return $count;
    }

    /**
     * Send password reset notification mail.
     *
     * @param $Customer 会員情報
     */
    public function sendPasswordResetCompleteMail(\Eccube\Entity\Customer $Customer, $password)
    {
        log_info('パスワード変更完了メール送信開始');

        $body = $this->app->renderView('Mail/reset_complete_mail.twig', array(
            'Customer' => $Customer,
            'password' => $password,
        ));

        $message = \Swift_Message::newInstance()
            ->setSubject('[' . $this->BaseInfo->getShopName() . '] パスワード変更のお知らせ')
            ->setFrom(array($this->BaseInfo->getEmail01() => $this->BaseInfo->getShopName()))
            ->setTo(array($Customer->getEmail()))
            ->setReplyTo($this->BaseInfo->getEmail03())
            ->setReturnPath($this->BaseInfo->getEmail04())
            ->setBody($body);

        $event = new EventArgs(
            array(
                'message' => $message,
                'Customer' => $Customer,
                'BaseInfo' => $this->BaseInfo,
                'password' => $password,
            ),
            null
        );
        $this->app['eccube.event.dispatcher']->dispatch(EccubeEvents::MAIL_PASSWORD_RESET_COMPLETE, $event);

        $count = $this->app->mail($message);

        log_info('パスワード変更完了メール送信完了', array('count' => $count));

        return $count;
    }

}
