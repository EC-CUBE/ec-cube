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

namespace Eccube\Controller\Admin\Customer;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Annotation\Inject;
use Eccube\Controller\AbstractController;
use Eccube\Entity\CustomerAddress;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\CustomerType;
use Eccube\Repository\CustomerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * @Route(service=CustomerEditController::class)
 */
class CustomerEditController extends AbstractController
{
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var array
     */
    protected $eccubeConfig;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject(CustomerRepository::class)
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @Inject("security.encoder_factory")
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    public function __construct(
        $eccubeConfig,
        CustomerRepository $customerRepository,
        EncoderFactoryInterface $encoderFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->eccubeConfig = $eccubeConfig;
        $this->customerRepository = $customerRepository;
        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/%admin_route%/customer/new", name="admin_customer_new")
     * @Route("/%admin_route%/customer/{id}/edit", requirements={"id" = "\d+"}, name="admin_customer_edit")
     * @Template("@admin/Customer/edit.twig")
     */
    public function index(Request $request, $id = null)
    {
        //$this->entityManager->getFilters()->enable('incomplete_order_status_hidden');
        // 編集
        if ($id) {
            $Customer = $this->customerRepository
                ->find($id);

            if (is_null($Customer)) {
                throw new NotFoundHttpException();
            }
            // 編集用にデフォルトパスワードをセット
            $previous_password = $Customer->getPassword();
            $Customer->setPassword($this->eccubeConfig['default_password']);
            // 新規登録
        } else {
            $Customer = $this->customerRepository->newCustomer();
            $CustomerAddress = new CustomerAddress();
            $Customer->setBuyTimes(0);
            $Customer->setBuyTotal(0);
        }

        // 会員登録フォーム
        $builder = $this->formFactory
            ->createBuilder(CustomerType::class, $Customer);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Customer' => $Customer,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                log_info('会員登録開始', array($Customer->getId()));

                $encoder = $this->encoderFactory->getEncoder($Customer);

                if ($Customer->getId() === null) {
                    $Customer->setSalt($encoder->createSalt());
                    $Customer->setSecretKey($this->customerRepository->getUniqueSecretKey());

                    $CustomerAddress->setName01($Customer->getName01())
                        ->setName02($Customer->getName02())
                        ->setKana01($Customer->getKana01())
                        ->setKana02($Customer->getKana02())
                        ->setCompanyName($Customer->getCompanyName())
                        ->setZip01($Customer->getZip01())
                        ->setZip02($Customer->getZip02())
                        ->setZipcode($Customer->getZip01() . $Customer->getZip02())
                        ->setPref($Customer->getPref())
                        ->setAddr01($Customer->getAddr01())
                        ->setAddr02($Customer->getAddr02())
                        ->setTel01($Customer->getTel01())
                        ->setTel02($Customer->getTel02())
                        ->setTel03($Customer->getTel03())
                        ->setFax01($Customer->getFax01())
                        ->setFax02($Customer->getFax02())
                        ->setFax03($Customer->getFax03())
                        ->setCustomer($Customer);

                    $this->entityManager->persist($CustomerAddress);
                }

                if ($Customer->getPassword() === $this->eccubeConfig['default_password']) {
                    $Customer->setPassword($previous_password);
                } else {
                    if ($Customer->getSalt() === null) {
                        $Customer->setSalt($encoder->createSalt());
                    }
                    $Customer->setPassword($encoder->encodePassword($Customer->getPassword(), $Customer->getSalt()));
                }

                $this->entityManager->persist($Customer);
                $this->entityManager->flush();

                log_info('会員登録完了', array($Customer->getId()));

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'Customer' => $Customer,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_COMPLETE, $event);

                $this->addSuccess('admin.customer.save.complete', 'admin');

                return $this->redirectToRoute('admin_customer_edit', array(
                    'id' => $Customer->getId(),
                ));
            } else {
                $this->addError('admin.customer.save.failed', 'admin');
            }
        }

        return [
            'form' => $form->createView(),
            'Customer' => $Customer,
        ];
    }
}
