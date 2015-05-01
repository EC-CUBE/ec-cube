<?php

namespace Eccube\Controller\Admin\Basis;

use Eccube\Application;
use Eccube\Controller\AbstractController;

class MailController extends AbstractController
{
    private $main_title;
    private $sub_title;

    public $form;

    public function __construct()
    {
        $this->main_title = '基本情報管理';
        $this->sub_title = 'メール管理';

        $this->tpl_mainno = 'basis';
        $this->tpl_subno = 'mail';
    }

    public function index(Application $app, $mailId = 0)
    {
        $Mail = $app['orm.em']
            ->getRepository('\Eccube\Entity\Mailtemplate')
            ->findOrCreate($mailId);
        $form = $app['form.factory']
            ->createBuilder('mail', $Mail)
            ->getForm();
        if ($mailId) {
            $form->get('template')->setData($Mail);
        }
        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);

            if ($form->isValid()) {
                $Mail = $form->getData();
                $app['orm.em']->persist($Mail);
                $app['orm.em']->flush();

                $app['session']->getFlashBag()->add('admin.mail.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_basis_mail'));
            }
        }

        return $app['twig']->render('Admin/Basis/mail.twig', array(
            'tpl_maintitle' => $this->main_title,
            'tpl_subtitle' => $this->sub_title,
            'Mail' => $Mail,
            'mail_id' => $mailId,
            'form' => $form->createView(),
        ));
    }
}
