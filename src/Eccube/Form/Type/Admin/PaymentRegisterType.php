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

use Eccube\Form\Type\PriceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentRegisterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('method', TextType::class, [
                'label' => 'paymentregister.label.payment_method',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('rule_min', PriceType::class, [
                'label' => 'paymentregister.label.rule',
                'currency' => 'JPY',
                'scale' => 0,
                'grouping' => true,
                'required' => false,
                'constraints' => [
                    // TODO 最大値でチェックしたい
                    // new Assert\Length(array(
                    //     'max' => $app['config']['int_len'],
                    // )),
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid',
                    ]),
                ],
            ])
            ->add('rule_max', PriceType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('payment_image_file', FileType::class, [
                'label' => 'paymentregister.label.logo_image',
                'mapped' => false,
                'required' => false,
            ])
            ->add('payment_image', HiddenType::class, [
                'required' => false,
            ])
            ->add('charge', PriceType::class, [
                'label' => 'paymentregister.label.commision',
            ])
            ->add('fixed', HiddenType::class)
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $ruleMax = $form['rule_max']->getData();
                $ruleMin = $form['rule_min']->getData();
                if (!empty($ruleMin) && !empty($ruleMax) && $ruleMax < $ruleMin) {
                    $message = trans('paymentregistertype.validate.rule', ['%price%' => $ruleMax]);
                    $form['rule_min']->addError(new FormError($message));
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Eccube\Entity\Payment',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'payment_register';
    }
}
