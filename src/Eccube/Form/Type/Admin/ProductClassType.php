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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Form\DataTransformer;
use Eccube\Form\Type\Master\DeliveryDateType;
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

/**
 * @FormType
 */
class ProductClassType extends AbstractType
{
    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var \Eccube\Application $app
     * @Inject(Application::class)
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
        $builder
            ->add('code', TextType::class, array(
                'label' => 'productclass.label.product_code',
                'required' => false,
            ))
            ->add('stock', NumberType::class, array(
                'label' => 'productclass.label.stock',
                'required' => false,
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid'
                    )),
                ),
            ))
            ->add('stock_unlimited', CheckboxType::class, array(
                'label' => 'productclass.label.unlimited',
                'value' => '1',
                'required' => false,
            ))
            ->add('sale_limit', NumberType::class, array(
                'label' => 'productclass.label.max_order',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => 10,
                    )),
                    new Assert\GreaterThanOrEqual(array(
                        'value' => 1,
                    )),
                    new Assert\Regex(array(
                        'pattern' => "/^\d+$/u",
                        'message' => 'form.type.numeric.invalid'
                    )),
                ),
            ))
            ->add('price01', PriceType::class, array(
                'label' => 'productclass.label.regular_price',
                'required' => false,
            ))
            ->add('price02', PriceType::class, array(
                'label' => 'productclass.label.sales_price',
            ))
            ->add('tax_rate', TextType::class, array(
                'label' => 'productclass.label.tax',
                'required' => false,
                'constraints' => array(
                    new Assert\Range(array('min' => 0, 'max' => 100)),
                    new Assert\Regex(array(
                        'pattern' => "/^\d+(\.\d+)?$/",
                        'message' => 'form.type.float.invalid'
                    )),
                ),
            ))
            ->add('delivery_fee', PriceType::class, array(
                'label' => 'productclass.label.shipping_charge',
                'required' => false,
            ))
            ->add('sale_type', SaleTypeType::class, array(
                'label' => 'productclass.label.sales_type',
                'multiple' => false,
                'expanded' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('delivery_date', DeliveryDateType::class, array(
                'label' => 'productclass.label.delivery_date',
                'required' => false,
                'placeholder' => 'productclass.placeholder.not_specified',
            ))
            ->add('add', CheckboxType::class, array(
                'label' => false,
                'required' => false,
                'value' => 1,
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();

                if (empty($data['stock_unlimited']) && is_null($data['stock'])) {
                    $form['stock_unlimited']->addError(new FormError('productclass.text.error.set_stock_quantitiy'));
                }
            });

        $transformer = new DataTransformer\EntityToIdTransformer(
            $this->entityManager,
            '\Eccube\Entity\ClassCategory'
        );
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
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\ProductClass',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_product_class';
    }
}
