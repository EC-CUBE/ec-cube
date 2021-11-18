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

namespace Eccube\Controller\Admin\Setting\Shop;

use Eccube\Controller\AbstractController;
use Eccube\Form\Type\Admin\OrderStatusSettingType;
use Eccube\Repository\Master\CustomerOrderStatusRepository;
use Eccube\Repository\Master\OrderStatusColorRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderStatusController extends AbstractController
{
    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @var OrderStatusColorRepository
     */
    protected $orderStatusColorRepository;

    /**
     * @var CustomerOrderStatusRepository
     */
    protected $customerOrderStatusRepository;

    public function __construct(
        OrderStatusRepository $orderStatusRepository,
        OrderStatusColorRepository $orderStatusColorRepository,
        CustomerOrderStatusRepository $customerOrderStatusRepository
    ) {
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderStatusColorRepository = $orderStatusColorRepository;
        $this->customerOrderStatusRepository = $customerOrderStatusRepository;
    }

    /**
     * 受注ステータス編集画面.
     *
     * @Route("/%eccube_admin_route%/setting/shop/order_status", name="admin_setting_shop_order_status", methods={"GET", "POST"})
     * @Template("@admin/Setting/Shop/order_status.twig")
     */
    public function index(Request $request)
    {
        $OrderStatuses = $this->orderStatusRepository->findBy([], ['sort_no' => 'ASC']);
        $builder = $this->formFactory->createBuilder();
        $builder
            ->add(
                'OrderStatuses',
                CollectionType::class,
                [
                    'entry_type' => OrderStatusSettingType::class,
                    'data' => $OrderStatuses,
                ]
            );
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form['OrderStatuses'] as $child) {
                $OrderStatus = $child->getData();
                $this->entityManager->persist($OrderStatus);

                $CustomerOrderStatus = $this->customerOrderStatusRepository->find($OrderStatus->getId());
                if (null !== $CustomerOrderStatus) {
                    $CustomerOrderStatus->setName($child['customer_order_status_name']->getData());
                    $this->entityManager->persist($CustomerOrderStatus);
                }

                $OrderStatusColor = $this->orderStatusColorRepository->find($OrderStatus->getId());
                if (null !== $OrderStatusColor) {
                    $OrderStatusColor->setName($child['color']->getData());
                    $this->entityManager->persist($OrderStatusColor);
                }
            }
            $this->entityManager->flush();

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_setting_shop_order_status');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
