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

namespace Eccube\Form\Type;

use Eccube\Form\Type\Master\CustomerStatusType;
use Eccube\Form\Type\Master\JobType;
use Eccube\Form\Type\Master\SexType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

// deprecated 3.1で削除予定
class CustomerType extends AbstractType
{
    /**
     * @var array
     */
    protected $eccubeConfig;

    /**
     * @var \Eccube\Application
     */
    protected $app;

    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            ->add('name', NameType::class, [
                'options' => [
                    'attr' => [
                        'maxlength' => $this->eccubeConfig['eccube_stext_len'],
                    ],
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                    ],
                ],
            ])
            ->add('kana', NameType::class, [
                'options' => [
                    'attr' => [
                        'maxlength' => $this->eccubeConfig['eccube_stext_len'],
                    ],
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                        new Assert\Regex([
                            'pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u',
                        ]),
                    ],
                ],
            ])
            ->add('company_name', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('zip', ZipType::class, [
                'required' => false,
            ])
            ->add('address', AddressType::class, [
                'help' => 'form.contact.address.help',
                'required' => false,
            ])
            ->add('tel', TelType::class, [
                'required' => false,
            ])
            ->add('fax', TelType::class, [
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(['strict' => true]),
                ],
            ])
            ->add('sex', SexType::class, [
                'required' => false,
            ])
            ->add('job', JobType::class, [
                'required' => false,
            ])
            ->add('birth', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => date('Y-m-d'),
                        'message' => 'form.type.select.selectisfuturedate',
                    ]),
                ],
            ])
            ->add('password', RepeatedType::class)
            ->add('status', CustomerStatusType::class, [
                'required' => false,
            ])
            ->add('note', TextareaType::class, [
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'この内容で登録する']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'customer';
    }
}
