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

use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\KanaType;
use Eccube\Form\Type\Master\CustomerStatusType;
use Eccube\Form\Type\Master\JobType;
use Eccube\Form\Type\Master\SexType;
use Eccube\Form\Type\NameType;
use Eccube\Form\Type\RepeatedPasswordType;
use Eccube\Form\Type\TelType;
use Eccube\Form\Type\ZipType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CustomerType extends AbstractType
{
    /**
     * @var array
     */
    protected $appConfig;

    /**
     * CustomerType constructor.
     * @param array $eccubeConfig
     */
    public function __construct(array $eccubeConfig)
    {
        $this->appConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', NameType::class, array(
                'required' => true,
            ))
            ->add('kana', KanaType::class, array(
                'required' => true,
            ))
            ->add('company_name', TextType::class, array(
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->appConfig['stext_len'],
                    ))
                ),
            ))
            ->add('zip', ZipType::class, array(
                'required' => true,
            ))
            ->add('address', AddressType::class, array(
                'required' => true,
            ))
            ->add('tel', TelType::class, array(
                'required' => true,
            ))
            ->add('fax', TelType::class, array(
                'required' => false,
            ))
            ->add('email', EmailType::class, array(
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    // configでこの辺りは変えられる方が良さそう
                    new Assert\Email(array('strict' => true)),
                    new Assert\Regex(array(
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                        'message' => 'form.type.graph.invalid',
                    )),
                ),
            ))
            ->add('sex', SexType::class, array(
                'required' => false,
            ))
            ->add('job', JobType::class, array(
                'required' => false,
            ))
            ->add('birth', BirthdayType::class, array(
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->appConfig['birth_max']),
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
                'constraints' => array(
                    new Assert\LessThanOrEqual(array(
                        'value' => date('Y-m-d'),
                        'message' => 'form.type.select.selectisfuturedate',
                    )),
                ),
            ))
            ->add('password', RepeatedPasswordType::class, array(
                // 'type' => 'password',
                'first_options'  => array(
                    'label' => 'パスワード',
                ),
                'second_options' => array(
                    'label' => 'パスワード(確認)',
                ),
            ))
            ->add('status', CustomerStatusType::class, array(
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add(
                'point',
                NumberType::class,
                [
                    'required' => false,
                    'label' => '所有ポイント',
                    'constraints' => array(
                        new Assert\Regex(array(
                            'pattern' => "/^\d+$/u",
                            'message' => 'form.type.numeric.invalid'
                        )),
                    ),
                ]
            )
            ->add('note', TextareaType::class, array(
                'label' => 'SHOP用メモ',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->appConfig['ltext_len'],
                    )),
                ),
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Customer',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_customer';
    }
}
