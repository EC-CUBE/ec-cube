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

use Doctrine\DBAL\Types\IntegerType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\Master\OrderStatusColorRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OrderStatusType extends AbstractType
{
    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @var OrderStatusColorRepository
     */
    protected $orderStatusColorRepository;

    public function __construct(
        OrderStatusRepository $orderStatusRepository,
        OrderStatusColorRepository $orderStatusColorRepository
    ) {
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderStatusColorRepository = $orderStatusColorRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('color', TextType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('display_order_count', CheckboxType::class, [
                'required' => false,
                'label' => false,
            ]);

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
