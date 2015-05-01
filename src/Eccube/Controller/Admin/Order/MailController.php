<?php

namespace Eccube\Controller\Admin\Order;

use Eccube\Application;
use Eccube\Entity\MailHistory;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MailController
{
    protected $title;
    protected $subtitle;

    public function __construct()
    {
        $this->title = '受注管理';
        $this->subtitle = 'メール配信';
    }

    public function index(Application $app, $orderId)
    {
        $Order =  $app['orm.em']
            ->getRepository('\Eccube\Entity\Order')
            ->find($orderId);

        if (is_null($Order)) {
            throw new HttpException('order not found.');
        }

        $MailHistories = $app['orm.em']
            ->getRepository('\Eccube\Entity\MailHistory')
            ->findBy(array('Order' => $orderId));

        $builder = $app['form.factory']->createBuilder('mail');
        $form = $builder->getForm();

        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            $mode = $app['request']->get('mode');

            // テンプレート変更の場合は. バリデーション前に内容差し替え.
            if ('change' === $mode) {
                if ($form->get('template')->isValid()) {
                    /** @var $data \Eccube\Entity\MailTemplate */
                    $MailTemplate = $form->get('template')->getData();
                    $form = $builder->getForm();
                    $form->get('template')->setData($MailTemplate);
                    $form->get('subject')->setData($MailTemplate->getSubject());
                    $form->get('header')->setData($MailTemplate->getHeader());
                    $form->get('footer')->setData($MailTemplate->getFooter());
                }
            }

            if ($form->isValid()) {
                switch ($mode) {
                    case 'confirm':
                        // フォームをFreezeして再生成.
                        $builder->setAttribute('freeze', true);
                        $form = $builder->getForm();
                        $form->handleRequest($app['request']);

                        $data = $form->getData();
                        $body = $this->createBody($app, $data['header'], $data['footer'], $Order);

                        return $app['view']->render('Admin/Order/mail_confirm.twig', array(
                            'form' => $form->createView(),
                            'title' => $this->title,
                            'subtitle' => $this->subtitle,
                            'body' => $body,
                            'Order' => $Order,
                        ));
                        break;
                    case 'send':
                        $data = $form->getData();
                        $body = $this->createBody($app, $data['header'], $data['footer'], $Order);

                        // TODO: 後でEventとして実装する
                        $message = $app['mail.message']
                            ->setSubject($data['subject'])
                            ->setFrom(array('sample@example.com'))
                            ->setCc($app['config']['mail_cc'])
                            ->setTo(array($Order->getEmail()))
                            ->setBody($body);
                        $app['mailer']->send($message);

                        // 送信履歴を保存.
                        $MailTemplate = $form->get('template')->getData();
                        $MailTemplateMaster = $app['orm.em']
                            ->getRepository('\Eccube\Entity\Master\MailTemplate')
                            ->find($MailTemplate->getId());
                        $MailHistory = new MailHistory();
                        $MailHistory
                            ->setSubject($data['subject'])
                            ->setMailBody($body)
                            ->setMailTemplate($MailTemplateMaster) // fixme mtb/dtb
                            ->setSendDate(new \DateTime())
                            ->setOrder($Order);
                        $app['orm.em']->persist($MailHistory);
                        $app['orm.em']->flush($MailHistory);

                        return $app->redirect($app['url_generator']->generate('admin_order'));
                        break;
                    default:
                        break;
                }
            }
        }

        return $app['view']->render('Admin/Order/mail.twig', array(
            'form' => $form->createView(),
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'Order' => $Order,
            'MailHistories' => $MailHistories
        ));
    }

    public function view(Application $app, $sendId)
    {
        $MailHistory = $app['orm.em']
            ->getRepository('\Eccube\Entity\MailHistory')
            ->find($sendId);

        if (is_null($MailHistory)) {
            throw new HttpException('history not found.');
        }

        return $app['view']->render('Admin/Order/mail_view.twig', array(
            'subject' => $MailHistory->getSubject(),
            'body' => $MailHistory->getMailBody()
        ));
    }

    protected function createBody($app, $header, $footer, $Order)
    {
        return $app['twig']->render('Mail/order.twig', array(
            'header' => $header,
            'footer' => $footer,
            'Order' => $Order,
        ));
    }
}
