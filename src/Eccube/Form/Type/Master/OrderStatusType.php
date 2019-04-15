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

namespace Eccube\Form\Type\Master;

use Eccube\Entity\Master\OrderStatus;
use Eccube\Form\Type\MasterType;
use Eccube\Repository\OrderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderStatusType extends AbstractType
{
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * OrderStatusType constructor.
     *
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var OrderStatus[] $OrderStatuses */
        $OrderStatuses = $options['choice_loader']->loadChoiceList()->getChoices();
        foreach ($OrderStatuses as $OrderStatus) {
            $id = $OrderStatus->getId();
            if ($OrderStatus->isDisplayOrderCount()) {
                $count = $this->orderRepository->countByOrderStatus($id);
                $view->vars['order_count'][$id]['display'] = true;
                $view->vars['order_count'][$id]['count'] = $count;
            } else {
                $view->vars['order_count'][$id]['display'] = false;
                $view->vars['order_count'][$id]['count'] = null;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => OrderStatus::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'order_status';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return MasterType::class;
    }
}
