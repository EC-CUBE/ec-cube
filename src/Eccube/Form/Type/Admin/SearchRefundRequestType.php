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
use Eccube\Form\Type\Master\RefundRequestStatusType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SearchRefundRequestType extends AbstractType
{
    public function __construct(private readonly EccubeConfig $eccubeConfig)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 複合検索（申請ID・注文番号・会員ID・会員名）
            ->add('multi', TextType::class, [
                'label' => 'admin.order.refund_request.multi_search_label',
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('status', RefundRequestStatusType::class, [
                'label' => 'admin.order.refund_request.status',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('create_date_start', DateType::class, [
                'label' => 'admin.order.refund_request.create_date__start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
            ])
            ->add('create_date_end', DateType::class, [
                'label' => 'admin.order.refund_request.create_date__end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
            ])
            ->add('update_date_start', DateType::class, [
                'label' => 'admin.order.refund_request.update_date__start',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
            ])
            ->add('update_date_end', DateType::class, [
                'label' => 'admin.order.refund_request.update_date__end',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'admin_search_refund_request';
    }
}
