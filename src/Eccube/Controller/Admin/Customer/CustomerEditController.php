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

namespace Eccube\Controller\Admin\Customer;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\CustomerType;
use Eccube\Repository\CustomerRepository;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/%eccube_admin_route%/customer/new", name="admin_customer_new", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/customer/{id}/edit", requirements={"id" = "\d+"}, name="admin_customer_edit", methods={"GET", "POST"})
     * @Template("@admin/Customer/edit.twig")
     */
    public function index(Request $request, $id = null)
    {
        $this->entityManager->getFilters()->enable('incomplete_order_status_hidden');
        // 編集
        if ($id) {
            /** @var Customer $Customer */
            $Customer = $this->customerRepository
                ->find($id);

            if (is_null($Customer)) {
                throw new NotFoundHttpException();
            }

            $oldStatusId = $Customer->getStatus()->getId();
            $Customer->setPlainPassword($this->eccubeConfig['eccube_default_password']);
        // 新規登録
        } else {
            $Customer = $this->customerRepository->newCustomer();

            $oldStatusId = null;
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
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_INITIALIZE);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('会員登録開始', [$Customer->getId()]);

            $encoder = $this->encoderFactory->getEncoder($Customer);

            if ($Customer->getPlainPassword() !== $this->eccubeConfig['eccube_default_password']) {
                if ($Customer->getSalt() === null) {
                    $Customer->setSalt($encoder->createSalt());
                    $Customer->setSecretKey($this->customerRepository->getUniqueSecretKey());
                }
                $Customer->setPassword($encoder->encodePassword($Customer->getPlainPassword(), $Customer->getSalt()));
            }

            // 退会ステータスに更新の場合、ダミーのアドレスに更新
            $newStatusId = $Customer->getStatus()->getId();
            if ($oldStatusId != $newStatusId && $newStatusId == CustomerStatus::WITHDRAWING) {
                $Customer->setEmail(StringUtil::random(60).'@dummy.dummy');
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
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_COMPLETE);

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_customer_edit', [
                'id' => $Customer->getId(),
            ]);
        }

        return [
            'form' => $form->createView(),
            'Customer' => $Customer,
        ];
    }
}
