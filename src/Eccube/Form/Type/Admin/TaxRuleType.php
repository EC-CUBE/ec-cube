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


namespace Eccube\Form\Type\Admin;

use Eccube\Annotation\FormType;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\Master\RoundingTypeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @FormType
 */
class TaxRuleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('option_product_tax_rule', ChoiceType::class, array(
                'label' => 'taxrule.text.radio.enabled',
                'choices' => array_flip(array(
                    '1' => 'taxrule.text.radio.enabled',
                    '0' => 'taxrule.text.radio.disabled',
                )),
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
            ))
            ->add('tax_rate', IntegerType::class, array(
                'label' => 'taxrule.label.tax_rate',
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
            ->add('rounding_type', RoundingTypeType::class, array(
                'label' => 'taxrule.label.tax_rules',
                'required' => true,
            ))
            ->add('apply_date', DateType::class, array(
                'label' => 'taxrule.label.tax_date',
                'required' => 'false',
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm',
                'years' => range(date('Y'), date('Y') + 2),
                'placeholder' => array(
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tax_rule';
    }
}
