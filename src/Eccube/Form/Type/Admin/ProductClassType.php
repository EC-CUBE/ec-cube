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
                'label' => '商品コード',
                'required' => false,
            ])
            ->add('stock', NumberType::class, [
                'label' => '在庫数',
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid'
                    ]),
                ],
            ])
            ->add('stock_unlimited', CheckboxType::class, [
                'label' => '無制限',
                'value' => '1',
                'required' => false,
            ])
            ->add('sale_limit', NumberType::class, [
                'label' => '販売制限数',
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
                        'message' => 'form.type.numeric.invalid'
                    ]),
                ],
            ])
            ->add('price01', PriceType::class, [
                'label' => '通常価格',
                'required' => false,
            ])
            ->add('price02', PriceType::class, [
                'label' => '販売価格',
            ])
            ->add('tax_rate', TextType::class, [
                'label' => '消費税率',
                'required' => false,
                'constraints' => [
                    new Assert\Range(['min' => 0, 'max' => 100]),
                    new Assert\Regex([
                        'pattern' => "/^\d+(\.\d+)?$/",
                        'message' => 'form.type.float.invalid'
                    ]),
                ],
            ])
            ->add('delivery_fee', PriceType::class, [
                'label' => '商品送料',
                'required' => false,
            ])
            ->add('sale_type', SaleTypeType::class, [
                'label' => '販売種別',
                'multiple' => false,
                'expanded' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('delivery_duration', DeliveryDurationType::class, [
                'label' => 'お届け可能日',
                'required' => false,
                'placeholder' => '指定なし',
            ])
            ->add('add', CheckboxType::class, [
                'label' => false,
                'required' => false,
                'value' => 1,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();

                if (empty($data['stock_unlimited']) && is_null($data['stock'])) {
                    $form['stock_unlimited']->addError(new FormError('在庫数を入力、もしくは在庫無制限を設定してください。'));
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
