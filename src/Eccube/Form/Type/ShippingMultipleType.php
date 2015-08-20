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


namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ShippingMultipleType extends AbstractType
{

    public $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            ->add('quantity', 'integer', array(
                'attr' => array(
                    'min' => 1,
                    'maxlength' => $this->app['config']['int_len'],
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\GreaterThanOrEqual(array(
                        'value' => 1,
                    )),
                    new Assert\Regex(array('pattern' => '/^\d+$/')),
                ),
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function ($event) use ($app) {
                /** @var \Eccube\Entity\ShipmentItem $data */
                $data = $event->getData();
                /** @var \Symfony\Component\Form\Form $form */
                $form = $event->getForm();

                $Order = $data->getShipping()->getOrder();
                $delives = $app['eccube.service.shopping']->getDeliveriesOrder($Order);

                $deliveries = array();
                foreach ($delives as $Delivery) {
                    $productType = $data->getProductClass()->getProductType();
                    if ($Delivery->getProductType()->getId() == $productType->getId()) {
                        $deliveries[] = $Delivery;
                    }
                }

                $delivery = $data->getShipping()->getDelivery();

                $form
                    ->add('delivery', 'entity', array(
                        'class' => 'Eccube\Entity\Delivery',
                        'property' => 'name',
                        'choices' => $deliveries,
                        'data' => $delivery,
                        'mapped' => false,
                    ));

            })
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\ShipmentItem',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shipping_multiple';
    }
}
