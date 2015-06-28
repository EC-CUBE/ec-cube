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

/**
 * Created by PhpStorm.
 * User: chihiro_adachi
 * Date: 15/04/18
 * Time: 19:50
 */

namespace Eccube\Form\Type;

use Eccube\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ShippingMultiType extends AbstractType
{
    private $app;
    private $config;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
        $this->config = $app['config'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $Customer = $this->app['user'];

        $builder
            ->add('product_class_id', 'hidden')
            ->add('quantity', 'integer', array(
                    'data' => 1,
                    'attr' => array(
                        'min' => 0,
                        'maxlength' => 100 // $this->config['int_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ))
            ->add('other_deliv', 'entity', array(
                    'class' => 'Eccube\Entity\CustomerAddress',
                    'property' => 'name01',
                    'query_builder' => function (\Eccube\Repository\CustomerAddressRepository $er) use ($Customer) {
                            return $er
                                ->createQueryBuilder('od')
                                ->where('od.Customer = :Customer')
                                ->orderBy("od.id", "ASC")
                                ->setParameter('Customer', $Customer);
                    },
                ))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shipping_multi';
    }
}
