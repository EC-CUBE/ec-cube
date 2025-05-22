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

namespace Eccube\Controller;

use Eccube\Entity\Customer;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\ContactType;
use Eccube\Repository\PageRepository;
use Eccube\Service\MailService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * ContactController constructor.
     *
     * @param MailService $mailService
     * @param PageRepository $pageRepository
     */
    public function __construct(
        MailService $mailService,
        PageRepository $pageRepository)
    {
        $this->mailService = $mailService;
        $this->pageRepository = $pageRepository;
    }

    /**
     * お問い合わせ画面.
     *
     * @Route("/contact", name="contact", methods={"GET", "POST"})
     * @Route("/contact", name="contact_confirm", methods={"GET", "POST"})
     * @Template("Contact/index.twig")
     */
    public function index(Request $request)
    {
        $builder = $this->formFactory->createBuilder(ContactType::class);

        if ($this->isGranted('ROLE_USER')) {
            /** @var Customer $user */
            $user = $this->getUser();
            $builder->setData(
                [
                    'name01' => $user->getName01(),
                    'name02' => $user->getName02(),
                    'kana01' => $user->getKana01(),
                    'kana02' => $user->getKana02(),
                    'postal_code' => $user->getPostalCode(),
                    'pref' => $user->getPref(),
                    'addr01' => $user->getAddr01(),
                    'addr02' => $user->getAddr02(),
                    'phone_number' => $user->getPhoneNumber(),
                    'email' => $user->getEmail(),
                ]
            );
        }

        // FRONT_CONTACT_INDEX_INITIALIZE
        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_CONTACT_INDEX_INITIALIZE);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            switch ($request->get('mode')) {
                case 'confirm':
                    return $this->render('Contact/confirm.twig', [
                        'form' => $form->createView(),
                        'Page' => $this->pageRepository->getPageByRoute('contact_confirm'),
                    ]);

                case 'complete':
                    $data = $form->getData();

                    $event = new EventArgs(
                        [
                            'form' => $form,
                            'data' => $data,
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch($event, EccubeEvents::FRONT_CONTACT_INDEX_COMPLETE);

                    $data = $event->getArgument('data');

                    // メール送信
                    $this->mailService->sendContactMail($data);

                    return $this->redirect($this->generateUrl('contact_complete'));
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * お問い合わせ完了画面.
     *
     * @Route("/contact/complete", name="contact_complete", methods={"GET"})
     * @Template("Contact/complete.twig")
     */
    public function complete()
    {
        return [];
    }
}
