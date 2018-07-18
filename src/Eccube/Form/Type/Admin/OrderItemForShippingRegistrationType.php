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

namespace Eccube\Form\Type\Admin;

use Eccube\Entity\OrderItem;
use Eccube\Repository\OrderItemRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OrderItemForShippingRegistrationType extends AbstractType
{
    /**
     * @var OrderItemRepository
     */
    protected $orderItemRepository;

    public function __construct(OrderItemRepository $orderItemRepository)
    {
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $orderItemId = $form['id']->getData();

            $OrderItem = $this->orderItemRepository->find($orderItemId);
            $event->setData($OrderItem);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderItem::class,
        ]);
    }
}
