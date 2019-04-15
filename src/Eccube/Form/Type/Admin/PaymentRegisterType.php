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
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentRegisterType extends AbstractType
{
    protected $app;

    public function __construct($app)
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
            ->add('method', 'text', array(
                'label' => '支払方法',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('rule_min', 'money', array(
                'label' => false,
                'currency' => 'JPY',
                'precision' => 0,
                'scale' => 0,
                'grouping' => true,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['int_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid'
                    )),
                ),
            ))
            ->add('rule_max', 'money', array(
                'label' => false,
                'currency' => 'JPY',
                'precision' => 0,
                'scale' => 0,
                'grouping' => true,
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['int_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid'
                    )),
                ),
            ))
            ->add('payment_image_file', 'file', array(
                'label' => 'ロゴ画像',
                'mapped' => false,
                'required' => false,
            ))
            ->add('payment_image', 'hidden', array(
                'required' => false,
            ))
            ->add('charge_flg', 'hidden')
            ->add('fix_flg', 'hidden')
            ->addEventListener(FormEvents::POST_BIND, function($event) {
                $form = $event->getForm();
                $ruleMax = $form['rule_max']->getData();
                $ruleMin = $form['rule_min']->getData();
                if (!empty($ruleMin) && !empty($ruleMax) && $ruleMax < $ruleMin) {
                    $form['rule_min']->addError(new FormError('利用条件(下限)は'.$ruleMax.'円以下にしてください。'));
                }
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($app) {
                $form = $event->getForm();
                /** @var \Eccube\Entity\Payment $Payment */
                $Payment = $event->getData();
                if (is_null($Payment) || $Payment->getChargeFlg() == 1) {
                    $form->add('charge', 'money', array(
                        'label' => '手数料',
                        'currency' => 'JPY',
                        'precision' => 0,
                        'scale' => 0,
                        'grouping' => true,
                        'constraints' => array(
                            new Assert\NotBlank(),
                            new Assert\Length(array(
                                'max' => $app['config']['int_len'],
                            )),
                            new Assert\Regex(array(
                                'pattern' => "/^\d+$/u",
                                'message' => 'form.type.numeric.invalid'
                            )),
                        ),
                    ));
                } else {
                    $form->add('charge', 'hidden');
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Payment',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'payment_register';
    }
}
