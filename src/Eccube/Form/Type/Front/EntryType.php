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

namespace Eccube\Form\Type\Front;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Customer;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\KanaType;
use Eccube\Form\Type\Master\JobType;
use Eccube\Form\Type\Master\SexType;
use Eccube\Form\Type\NameType;
use Eccube\Form\Type\RepeatedEmailType;
use Eccube\Form\Type\RepeatedPasswordType;
use Eccube\Form\Type\PhoneNumberType;
use Eccube\Form\Type\PostalType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EntryType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * EntryType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', NameType::class, [
                'required' => true,
            ])
            ->add('kana', KanaType::class, [])
            ->add('company_name', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('postal_code', PostalType::class)
            ->add('address', AddressType::class)
            ->add('phone_number', PhoneNumberType::class, [
                'required' => true,
            ])
            ->add('email', RepeatedEmailType::class)
            ->add('password', RepeatedPasswordType::class)
            ->add('birth', BirthdayType::class, [
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $this->eccubeConfig['eccube_birth_max']),
                'widget' => 'choice',
                'format' => 'yyyy/MM/dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => date('Y-m-d', strtotime('-1 day')),
                        'message' => 'form_error.select_is_future_or_now_date',
                    ]),
                ],
            ])
            ->add('sex', SexType::class, [
                'required' => false,
            ])
            ->add('job', JobType::class, [
                'required' => false,
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $Customer = $event->getData();
            if ($Customer instanceof Customer && !$Customer->getId()) {
                $form = $event->getForm();

                $form->add('user_policy_check', CheckboxType::class, [
                        'required' => true,
                        'label' => null,
                        'mapped' => false,
                        'constraints' => [
                            new Assert\NotBlank(),
                        ],
                    ]);
            }
        }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Eccube\Entity\Customer',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        // todo entry,mypageで共有されているので名前を変更する
        return 'entry';
    }
}
