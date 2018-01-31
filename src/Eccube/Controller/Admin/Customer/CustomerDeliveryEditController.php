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

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Annotation\Inject;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Customer;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\CustomerAddressType;
use Eccube\Repository\CustomerAddressRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route(service=CustomerDeliveryEditController::class)
 */
class CustomerDeliveryEditController extends AbstractController
{
    /**
     * @var CustomerAddressRepository
     */
    protected $customerAddressRepository;

    public function __construct(
        CustomerAddressRepository $customerAddressRepository
    ) {
        $this->customerAddressRepository = $customerAddressRepository;
    }

    /**
     * お届け先編集画面.
     *
     * @Route("/%admin_route%/customer/{id}/delivery/new", name="admin_customer_delivery_new", requirements={"id" = "\d+"})
     * @Route("/%admin_route%/customer/{id}/delivery/{did}/edit", name="admin_customer_delivery_edit", requirements={"id" = "\d+", "did" = "\d+"})
     * @Template("@admin/Customer/delivery_edit.twig")
     */
    public function edit(Request $request, Customer $Customer, $did = null)
    {
        // 配送先住所最大値判定
        // $idが存在する際は、追加処理ではなく、編集の処理ため本ロジックスキップ
        $addressMax = $this->eccubeConfig['deliv_addr_max'];
        if (is_null($did)) {
            $addressCurrNum = count($Customer->getCustomerAddresses());
            $addressMax = $this->eccubeConfig['deliv_addr_max'];
            if ($addressCurrNum >= $addressMax) {
                throw new NotFoundHttpException('お届け先の登録数の上限を超えています');
            }
        } else {
            $CustomerAddress = $this->customerAddressRepository->find($did);
            if (is_null($CustomerAddress) || $CustomerAddress->getCustomer()->getId() != $Customer->getId()) {
                throw new NotFoundHttpException();
            }
        }

        $CustomerAddress = $this->customerAddressRepository->findOrCreateByCustomerAndId($Customer, $did);

        $builder = $this->formFactory
            ->createBuilder(CustomerAddressType::class, $CustomerAddress);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Customer' => $Customer,
                'CustomerAddress' => $CustomerAddress,
            ),
            $request
        );

        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_DELIVERY_EDIT_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                log_info('お届け先登録開始', array($did));

                $this->entityManager->persist($CustomerAddress);
                $this->entityManager->flush();

                log_info('お届け先登録完了', array($did));

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'Customer' => $Customer,
                        'CustomerAddress' => $CustomerAddress,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_DELIVERY_EDIT_INDEX_COMPLETE, $event);

                $this->addSuccess('admin.customer.delivery.save.complete', 'admin');

                return $this->redirect($this->generateUrl('admin_customer_delivery_edit', array(
                    'id' => $Customer->getId(),
                    'did' => $CustomerAddress->getId(),
                )));
            } else {
                $this->addError('admin.customer.delivery.save.failed', 'admin');
            }
        }

        return [
            'form' => $form->createView(),
            'Customer' => $Customer,
            'CustomerAddress' => $CustomerAddress,
        ];

    }

    /**
     * @Method("DELETE")
     * @Route("/%admin_route%/customer/{id}/delivery/{did}/delete", requirements={"id" = "\d+", "did" = "\d+"}, name="admin_customer_delivery_delete")
     */
    public function delete(Request $request, Customer $Customer, $did)
    {
        $this->isTokenValid();

        log_info('お届け先削除開始', array($did));

        $CustomerAddress = $this->customerAddressRepository->find($did);
        if (is_null($CustomerAddress)) {
            throw new NotFoundHttpException();
        } else {
            if ($CustomerAddress->getCustomer()->getId() != $Customer->getId()) {
                $this->deleteMessage();
                return $this->redirect($this->generateUrl('admin_customer_edit', array('id' => $Customer->getId())));
            }
        }

        try {
            $this->customerAddressRepository->delete($CustomerAddress);
            $this->addSuccess('admin.customer.delivery.delete.complete', 'admin');
        } catch (ForeignKeyConstraintViolationException $e) {
            log_error('お届け先削除失敗', [$e], 'admin');

            $message = $this->trans('admin.delete.failed.foreign_key', ['%name%' => 'お届け先']);
            $this->addError($message, 'admin');
        }

        log_info('お届け先削除完了', array($did));

        $event = new EventArgs(
            array(
                'Customer' => $Customer,
                'CustomerAddress' => $CustomerAddress,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_DELIVERY_DELETE_COMPLETE, $event);

        return $this->redirect($this->generateUrl('admin_customer_edit', array('id' => $Customer->getId())));
    }

}
