<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\Customer;

use Eccube\Controller\AbstractController;
use Eccube\Entity\CustomerAddress;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\CustomerType;
use Eccube\Repository\CustomerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class CustomerEditController extends AbstractController
{
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
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->customerRepository = $customerRepository;
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @Route("/%eccube_admin_route%/customer/new", name="admin_customer_new")
     * @Route("/%eccube_admin_route%/customer/{id}/edit", requirements={"id" = "\d+"}, name="admin_customer_edit")
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
            $Customer->setPassword($this->eccubeConfig['eccube_default_password']);
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
            [
                'builder' => $builder,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                log_info('会員登録開始', [$Customer->getId()]);

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
                        ->setZipcode($Customer->getZip01().$Customer->getZip02())
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

                if ($Customer->getPassword() === $this->eccubeConfig['eccube_default_password']) {
                    $Customer->setPassword($previous_password);
                } else {
                    if ($Customer->getSalt() === null) {
                        $Customer->setSalt($encoder->createSalt());
                        $Customer->setSecretKey($this->customerRepository->getUniqueSecretKey());
                    }
                    $Customer->setPassword($encoder->encodePassword($Customer->getPassword(), $Customer->getSalt()));
                }

                $this->entityManager->persist($Customer);
                $this->entityManager->flush();

                log_info('会員登録完了', [$Customer->getId()]);

                $event = new EventArgs(
                    [
                        'form' => $form,
                        'Customer' => $Customer,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_COMPLETE, $event);

                $this->addSuccess('admin.customer.save.complete', 'admin');

                return $this->redirectToRoute('admin_customer_edit', [
                    'id' => $Customer->getId(),
                ]);
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
