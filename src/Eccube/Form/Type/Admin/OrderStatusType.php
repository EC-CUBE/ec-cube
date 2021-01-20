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

namespace Eccube\Form\Type\Admin;

use Eccube\Entity\Master\OrderStatus;
use Eccube\Form\Type\ToggleSwitchType;
use Eccube\Repository\Master\OrderStatusColorRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OrderStatusType extends AbstractType
{
    /**
     * @var OrderStatusColorRepository
     */
    protected $orderStatusColorRepository;

    public function __construct(
        OrderStatusColorRepository $orderStatusColorRepository
    ) {
        $this->orderStatusColorRepository = $orderStatusColorRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                ],
            ])
            ->add('color', TextType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]),
                ],
            ])
            ->add('display_order_count', ToggleSwitchType::class, [
                'required' => false,
                'label_on' => '',
                'label_off' => '',
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (null === $data) {
                return;
            }

            $OrderStatusColor = $this->orderStatusColorRepository->find($data->getId());
            if (null !== $OrderStatusColor) {
                $form->get('color')->setData($OrderStatusColor->getName());
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderStatus::class,
        ]);
    }
}
