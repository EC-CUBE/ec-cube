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

use Eccube\Entity\RefundRequest;
use Eccube\Service\RefundRequestStateMachine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RefundRequestEditType extends AbstractType
{
    public function __construct(
        private readonly RefundRequestStateMachine $refundRequestStateMachine,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var RefundRequest $RefundRequest */
        $RefundRequest = $options['refund_request'];
        $transitions = $this->refundRequestStateMachine->getAvailableTransitions($RefundRequest);

        $choices = [];
        foreach ($transitions as $transitionName => $Status) {
            $choices[(string) $Status] = $transitionName;
        }

        $builder
            ->add('admin_note', TextareaType::class, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('transition', ChoiceType::class, [
                'required' => false,
                'mapped' => false,
                'choices' => $choices,
                'placeholder' => 'admin.order.refund_request.transition_placeholder',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'refund_request' => null,
        ]);
        $resolver->setAllowedTypes('refund_request', [RefundRequest::class, 'null']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'admin_refund_request_edit';
    }
}
