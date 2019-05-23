<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

use Eccube\Entity\CustomerAddress;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

class ShippingMultipleItemType extends AbstractType
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
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($app) {
                $form = $event->getForm();

                if ($app->isGranted('IS_AUTHENTICATED_FULLY')) {
                    // 会員の場合、CustomerAddressを設定
                    $Customer = $app->user();
                    $form->add('customer_address', 'entity', array(
                        'class' => 'Eccube\Entity\CustomerAddress',
                        'property' => 'shippingMultipleDefaultName',
                        'query_builder' => function (EntityRepository $er) use ($Customer) {
                            return $er->createQueryBuilder('ca')
                                ->where('ca.Customer = :Customer')
                                ->orderBy("ca.id", "ASC")
                                ->setParameter('Customer', $Customer);
                        },
                        'constraints' => array(
                            new Assert\NotBlank(),
                        ),
                    ));
                } else {
                    // 非会員の場合、セッションに設定されたCustomerAddressを設定
                    if ($app['session']->has('eccube.front.shopping.nonmember.customeraddress')) {
                        $customerAddresses = $app['session']->get('eccube.front.shopping.nonmember.customeraddress');
                        $customerAddresses = json_decode($customerAddresses, true);

                        $addresses = array();
                        /** @var \Eccube\Entity\CustomerAddress $value */
                        foreach ($customerAddresses as $value) {

                            $customerAddressArray = (array) $value;
                            $customerAddressArray['Pref'] = (array) $customerAddressArray['Pref'];

                            $CustomerAddress = new CustomerAddress();
                            $CustomerAddress->setPropertiesFromArray($customerAddressArray);
                            $CustomerAddress->setCustomer($app['eccube.service.shopping']->getNonMember('eccube.front.shopping.nonmember'));
                            $CustomerAddress->setPref($app['eccube.repository.master.pref']->find($customerAddressArray['Pref']['id']));

                            $addresses[] = $CustomerAddress->getShippingMultipleDefaultName();
                        }
                        $form->add('customer_address', 'choice', array(
                            'choices' => $addresses,
                            'constraints' => array(
                                new Assert\NotBlank(),
                            ),
                        ));
                    }
                }
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                /** @var \Eccube\Entity\Shipping $data */
                $data = $event->getData();
                /** @var \Symfony\Component\Form\Form $form */
                $form = $event->getForm();

                if (is_null($data)) {
                    return;
                }

                $quantity = 0;
                // Check all shipment items
                foreach ($data->getShipmentItems() as $ShipmentItem) {
                    // Check item distinct for each quantity
                    if ($data->getProductClassOfTemp()->getId() == $ShipmentItem->getProductClass()->getId()) {
                        $quantity += $ShipmentItem->getQuantity();
                    }
                }
                $form['quantity']->setData($quantity);

            });

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shipping_multiple_item';
    }
}
