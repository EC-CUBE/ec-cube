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
use Symfony\Component\Validator\Constraints as Assert;

class ShoppingType extends AbstractType
{
    public $app;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delivery', 'entity', array(
                    'class' => 'Eccube\Entity\Deliv',
                    'property' => "name"))
            ->add('payment', 'entity', array(
                'class' => 'Eccube\Entity\Payment',
                'property' => "method"))
            ->add('delivery_date', 'entity', array(
                    'class' => 'Eccube\Entity\Master\DeliveryDate',
                    'property' => "name"))
            ->add('delivery_time', 'entity', array(
                    'class' => 'Eccube\Entity\DelivTime',
                    'property' => "deliv_time"))
            ->add('message', 'textarea', array(
                    'required' => false,
                    'constraints' => array(
                        new Assert\Length(array('min' => 0, 'max' => 500))),
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shopping';
    }
}
