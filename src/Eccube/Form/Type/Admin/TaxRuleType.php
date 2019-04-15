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


namespace Eccube\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TaxRuleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('option_product_tax_rule', 'choice', array(
                'label' => '商品別税率機能',
                'choices' => array(
                    '1' => '有効',
                    '0' => '無効',
                ),
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
            ))
            ->add('tax_rate', 'integer', array(
                'label' => '消費税率',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Range(array('min' => 0, 'max' => 100)),
                    new Assert\Regex(array(
                        'pattern' => "/^\d+(\.\d+)?$/u",
                        'message' => 'form.type.float.invalid'
                    )),
                ),
            ))
            ->add('calc_rule', 'calc_rule', array(
                'label' => '課税規則',
                'required' => true,
            ))
            ->add('apply_date', 'date', array(
                'label' => '適用日時',
                'required' => 'false',
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm',
                'years' => range(date('Y'), date('Y') + 2),
                'empty_value' => array(
                    'year' => '----',
                    'month' => '--',
                    'day' => '--',
                    'hours' => '--',
                    'minutes' => '--'
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tax_rule';
    }
}
