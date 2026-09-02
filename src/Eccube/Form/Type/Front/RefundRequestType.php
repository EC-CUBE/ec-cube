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

namespace Eccube\Form\Type\Front;

use Eccube\Entity\RefundRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RefundRequestType extends AbstractType
{
    /**
     * 返品申請で許可する MIME タイプ.
     */
    public const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'video/mp4',
        'video/quicktime',
        'video/x-msvideo',
    ];

    /** 1 申請あたりの最大ファイル数. */
    public const MAX_FILE_COUNT = 3;

    /** 1 ファイルあたりの最大サイズ. */
    public const MAX_FILE_SIZE = '15M';

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $options
     */
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $maxQuantity = $options['max_quantity'];

        $builder
            // quantity は decimal(string) のため unmapped にし、Controller で文字列化してセットする
            ->add('quantity', IntegerType::class, [
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThan(0),
                    new Assert\LessThanOrEqual(value: $maxQuantity, message: 'form_error.refund_request.quantity_exceed'),
                ],
            ])
            ->add('reason', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: 4000),
                ],
            ])
            ->add('files', FileType::class, [
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'constraints' => [
                    new Assert\Count(max: self::MAX_FILE_COUNT, maxMessage: 'form_error.refund_request.max_files'),
                    new Assert\All([
                        new Assert\File(maxSize: self::MAX_FILE_SIZE, maxSizeMessage: 'form_error.refund_request.file_size', mimeTypes: self::ALLOWED_MIME_TYPES, mimeTypesMessage: 'form_error.refund_request.file_type'),
                    ]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RefundRequest::class,
            'max_quantity' => 1,
        ]);
        $resolver->setAllowedTypes('max_quantity', ['int', 'string']);
    }
}
