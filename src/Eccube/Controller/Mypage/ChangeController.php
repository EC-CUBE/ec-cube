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


namespace Eccube\Controller\Mypage;

use Eccube\Annotation\Inject;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\EntryType;
use Eccube\Repository\CustomerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @Route(service=ChangeController::class)
 */
class ChangeController extends AbstractController
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    public function __construct(
        CustomerRepository $customerRepository,
        EncoderFactoryInterface $encoderFactory,
        TokenStorageInterface $tokenStorage
    ) {
        $this->customerRepository = $customerRepository;
        $this->encoderFactory = $encoderFactory;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * 会員情報編集画面.
     *
     * @Route("/mypage/change", name="mypage_change")
     * @Template("Mypage/change.twig")
     */
    public function index(Request $request)
    {
        $Customer = $this->getUser();
        $LoginCustomer = clone $Customer;
        $this->entityManager->detach($LoginCustomer);

        $previous_password = $Customer->getPassword();
        $Customer->setPassword($this->eccubeConfig['default_password']);

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $this->formFactory->createBuilder(EntryType::class, $Customer);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Customer' => $Customer,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_INITIALIZE, $event);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            log_info('会員編集開始');

            if ($Customer->getPassword() === $this->eccubeConfig['default_password']) {
                $Customer->setPassword($previous_password);
            } else {
                $encoder = $this->encoderFactory->getEncoder($Customer);
                if ($Customer->getSalt() === null) {
                    $Customer->setSalt($encoder->createSalt());
                }
                $Customer->setPassword(
                    $encoder->encodePassword($Customer->getPassword(), $Customer->getSalt())
                );
            }
            $this->entityManager->flush();

            log_info('会員編集完了');

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'Customer' => $Customer,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_COMPLETE, $event);

            return $this->redirect($this->generateUrl('mypage_change_complete'));
        }
        dump($this->tokenStorage);
        $this->tokenStorage->getToken()->setUser($LoginCustomer);

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * 会員情報編集完了画面.
     *
     * @Route("/mypage/change_complete", name="mypage_change_complete")
     * @Template("Mypage/change_complete.twig")
     */
    public function complete(Request $request)
    {
        return [];
    }
}
