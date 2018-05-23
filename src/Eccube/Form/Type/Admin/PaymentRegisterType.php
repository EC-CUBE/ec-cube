<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
