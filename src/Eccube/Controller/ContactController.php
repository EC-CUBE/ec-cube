<?php

namespace Eccube\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class ContactController
{

    private $title;

    public function __construct()
    {
        $this->title = 'お問い合わせ';
    }

    public function index(Application $app, Request $request)
    {

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $app['form.factory']->createBuilder('contact');

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($app['security']->isGranted('ROLE_USER')) {
            /* @var $user \Eccube\Entity\Customer */
            $user = $app['user'];
            $form->setData(array(
                'name01' => $user->getName01(),
                'name02' => $user->getName02(),
                'kana01' => $user->getKana01(),
                'kana02' => $user->getKana02(),
                'zip01' => $user->getZip01(),
                'zip02' => $user->getZip02(),
                'pref' => $user->getPref(),
                'addr01' => $user->getAddr01(),
                'addr02' => $user->getAddr02(),
                'tel01' => $user->getTel01(),
                'tel02' => $user->getTel02(),
                'tel03' => $user->getTel03(),
                'email' => $user->getEmail(),
            ));
        }

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                switch ($request->get('mode')) {
                    case 'confirm':
                        $builder->setAttribute('freeze', true);
                        $form = $builder->getForm();
                        $form->handleRequest($request);

                        return $app['twig']->render('Contact/confirm.twig', array(
                            'title' => $this->title,
                            'form' => $form->createView(),
                        ));
                        break;
                    case 'complete':
                        $data = $form->getData();

                        // TODO: 後でEventとして実装する
                        $message = $app['mail.message']
                            ->setSubject('[EC-CUBE3] お問い合わせを受け付けました。')
                            ->setFrom(array('sample@example.com'))
                            ->setCc($app['config']['mail_cc'])
                            ->setTo(array($data['email']))
                            ->setBody($data['contents']);
                        $app['mailer']->send($message);

                        return $app->redirect($app['url_generator']->generate('contact_complete'));
                        break;
                }
            }
        }

        return $app['twig']->render('Contact/index.twig', array(
            'title' => $this->title,
            'form' => $form->createView(),
        ));
    }

    public function complete(Application $app)
    {
        return $app['twig']->render('Contact/complete.twig', array(
            'title' => $this->title,
        ));
    }
}
