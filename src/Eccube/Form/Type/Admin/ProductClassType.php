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

use Eccube\Form\DataTransformer;
use Eccube\Form\Type\Master\DeliveryDurationType;
use Eccube\Form\Type\Master\SaleTypeType;
use Eccube\Form\Type\PriceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\ClassCategory;

class ProductClassType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * ProductClassType constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('stock', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid',
                    ]),
                ],
            ])
            ->add('stock_unlimited', CheckboxType::class, [
                'label' => 'admin.product.stock_unlimited__short',
                'value' => '1',
                'required' => false,
            ])
            ->add('sale_limit', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => 10,
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 1,
                    ]),
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid',
                    ]),
                ],
            ])
            ->add('price01', PriceType::class, [
                'required' => false,
            ])
            ->add('price02', PriceType::class, [
            ])
            ->add('tax_rate', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Range(['min' => 0, 'max' => 100]),
                    new Assert\Regex([
                        'pattern' => "/^\d+(\.\d+)?$/",
                        'message' => 'form.type.float.invalid',
                    ]),
                ],
            ])
            ->add('delivery_fee', PriceType::class, [
                'required' => false,
            ])
            ->add('sale_type', SaleTypeType::class, [
                'multiple' => false,
                'expanded' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('delivery_duration', DeliveryDurationType::class, [
                'required' => false,
                'placeholder' => 'common.select__unspecified',
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();

                if (empty($data['stock_unlimited']) && is_null($data['stock'])) {
                    $form['stock_unlimited']->addError(new FormError(trans('admin.product.product_class_set_stock_quantity')));
                }
            });

        $transformer = new DataTransformer\EntityToIdTransformer($this->entityManager, ClassCategory::class);
        $builder
            ->add($builder->create('ClassCategory1', HiddenType::class)
                ->addModelTransformer($transformer)
            )
            ->add($builder->create('ClassCategory2', HiddenType::class)
                ->addModelTransformer($transformer)
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Eccube\Entity\ProductClass',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_product_class';
    }
}
