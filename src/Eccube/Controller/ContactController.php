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


namespace Eccube\Controller;

use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\ContactType;
use Eccube\Service\MailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Component
 * @Route(service=ContactController::class)
 */
class ContactController
{
    /**
     * @Inject(MailService::class)
     * @var MailService
     */
    protected $mailService;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * お問い合わせ画面.
     *
     * @Route("/contact", name="contact")
     * @Template("Contact/index.twig")
     */
    public function index(Application $app, Request $request)
    {
        $builder = $this->formFactory->createBuilder(ContactType::class);

        if ($app->isGranted('ROLE_USER')) {
            $user = $app['user'];
            $builder->setData(
                array(
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
                )
            );
        }

        // FRONT_CONTACT_INDEX_INITIALIZE
        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_CONTACT_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            switch ($request->get('mode')) {
                case 'confirm':
                    $builder->setAttribute('freeze', true);
                    $form = $builder->getForm();
                    $form->handleRequest($request);
                    $title = 'お問い合わせ(確認ページ)';

                    return $app->render(
                        'Contact/confirm.twig',
                        array(
                            'form' => $form->createView(),
                            'title' => $title,
                        )
                    );

                case 'complete':

                    $data = $form->getData();

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'data' => $data,
                        ),
                        $request
                    );
                    $this->eventDispatcher->dispatch(EccubeEvents::FRONT_CONTACT_INDEX_COMPLETE, $event);

                    $data = $event->getArgument('data');

                    // メール送信
                    $this->mailService->sendContactMail($data);

                    return $app->redirect($app->url('contact_complete'));
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * お問い合わせ完了画面.
     *
     * @Route("/contact/complete", name="contact_complete")
     * @Template("Contact/complete.twig")
     */
    public function complete(Application $app, Request $request)
    {
        return [];
    }
}
