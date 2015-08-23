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

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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

        $Customer = $this->app->user();

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
            ->add('customer_address', 'entity', array(
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
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function ($event) use ($app) {
                /** @var \Eccube\Entity\Shipping $data */
                $data = $event->getData();
                /** @var \Symfony\Component\Form\Form $form */
                $form = $event->getForm();
                $form['quantity']->setData(3);

            })
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) use ($app) {
                /** @var \Eccube\Entity\Shipping $data */
                $data = $event->getData();
                error_log(get_class($data));
                /** @var \Symfony\Component\Form\Form $form */
                $form = $event->getForm();
                $data1 = $form['quantity']->getData();
                Debug::dump($data1);
                $event->setData($data1);
            })
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shipping_multiple_item';
    }
}
