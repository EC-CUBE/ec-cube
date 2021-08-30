<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Master\Authority;
use Eccube\Entity\Master\Work;
use Eccube\Entity\Member;
use Eccube\Form\Type\RepeatedPasswordType;
use Eccube\Form\Type\ToggleSwitchType;
use Eccube\Repository\MemberRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MemberType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     * MemberType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param MemberRepository $memberRepository
     */
    public function __construct(
        EccubeConfig $eccubeConfig,
        MemberRepository $memberRepository
    ) {
        $this->eccubeConfig = $eccubeConfig;
        $this->memberRepository = $memberRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('department', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('plain_password', RepeatedPasswordType::class, [
                'first_options' => [
                    'label' => 'admin.setting.system.member.password',
                ],
                'second_options' => [
                    'label' => 'admin.setting.system.member.password',
                ],
            ])
            ->add('Authority', EntityType::class, [
                'class' => 'Eccube\Entity\Master\Authority',
                'expanded' => false,
                'multiple' => false,
                'placeholder' => 'admin.common.select',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('Work', EntityType::class, [
                'class' => 'Eccube\Entity\Master\Work',
                'expanded' => true,
                'multiple' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('two_factor_auth_enabled', ToggleSwitchType::class, [
            ]);

        // login idの入力は新規登録時のみとし、編集時はdisabledにする
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $options = [
                'constraints' => [
                    new Assert\Length([
                        'min' => $this->eccubeConfig['eccube_id_min_len'],
                        'max' => $this->eccubeConfig['eccube_id_max_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                        'message' => 'form_error.graph_only',
                    ]),
                ],
            ];

            if ($data->getId() === null) {
                $options['constraints'][] = new Assert\NotBlank();
            } else {
                $options['required'] = false;
                $options['mapped'] = false;
                $options['attr'] = [
                    'disabled' => 'disabled'
                ];
                $options['data'] = $data->getLoginId();
            }

            $form->add('login_id', TextType::class, $options);
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var Member $Member */
            $Member = $event->getData();

            // 編集時に, 非稼働で更新した場合にチェック.
            if ($Member->getId() && $Member->getWork()->getId() == Work::NON_ACTIVE) {
                // 自身を除いた稼働メンバーの件数
                $count = $this->memberRepository
                    ->createQueryBuilder('m')
                    ->select('COUNT(m)')
                    ->where('m.Work = :Work AND m.Authority = :Authority AND m.id <> :Member')
                    ->setParameter('Work', Work::ACTIVE)
                    ->setParameter('Authority', Authority::ADMIN)
                    ->setParameter('Member', $Member)
                    ->getQuery()
                    ->getSingleScalarResult();

                if ($count < 1) {
                    $form = $event->getForm();
                    $form['Work']->addError(new FormError(trans('admin.setting.system.member.work_can_not_change')));
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Eccube\Entity\Member',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_member';
    }
}
