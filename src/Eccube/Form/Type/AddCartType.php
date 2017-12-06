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


namespace Eccube\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Entity\CartItem;
use Eccube\Entity\ProductClass;
use Eccube\Form\DataTransformer\EntityToIdTransformer;
use Eccube\Repository\ProductClassRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * @FormType
 */
class AddCartType extends AbstractType
{
    /**
     * @var array
     * @Inject("config")
     */
    protected $config;

    /**
     * @var EntityManager
     * @Inject("orm.em")
     */
    protected $em;

    /**
     * @var \Eccube\Entity\Product
     */
    protected $Product = null;

    /**
     * @var ProductClassRepository
     * @Inject(ProductClassRepository::class)
     */
    protected $productClassRepository;

    protected $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var $Product \Eccube\Entity\Product */
        $Product = $options['product'];
        $this->Product = $Product;
        $ProductClass = $Product->getProductClasses()->first();

        $builder
            ->add(
                $builder
                    ->create('ProductClass', HiddenType::class, [
                        'data_class' => null,
                        'data' => $ProductClass,
                    ])
                ->addModelTransformer(new EntityToIdTransformer($this->doctrine->getManager(), ProductClass::class))
            )
        ;

        if ($Product->getStockFind()) {
            $builder
                ->add('quantity', IntegerType::class, array(
                    'data' => 1,
                    'attr' => array(
                        'min' => 1,
                        'maxlength' => $this->config['int_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                        new Assert\GreaterThanOrEqual(array(
                            'value' => 1,
                        )),
                        new Assert\Regex(array('pattern' => '/^\d+$/')),
                    ),
                ))
            ;
            if ($Product && $Product->getProductClasses()) {
                if (!is_null($Product->getClassName1())) {
                    $builder->add('classcategory_id1', ChoiceType::class, [
                        'label' => $Product->getClassName1(),
                        'choices' => ['選択してください' => '__unselected'] + $Product->getClassCategories1AsFlip(),
                        'mapped' => false,
                    ]);
                }
                if (!is_null($Product->getClassName2())) {
                    $builder->add('classcategory_id2', ChoiceType::class, [
                        'label' => $Product->getClassName2(),
                        'choices' => ['選択してください' => '__unselected'],
                        'mapped' => false,
                    ]);
                }
            }

            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($Product) {
                $data = $event->getData();
                $form = $event->getForm();
                if (isset($data['classcategory_id1']) && !is_null($Product->getClassName2())) {
                    if ($data['classcategory_id1']) {
                        $form->add('classcategory_id2', ChoiceType::class, [
                            'label' => $Product->getClassName2(),
                            'choices' => ['選択してください' => '__unselected'] + $Product->getClassCategories2AsFlip($data['classcategory_id1']),
                            'mapped' => false,
                        ]);
                    }
                }
            });

            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                /** @var CartItem $CartItem */
                $CartItem = $event->getData();
                $ProductClass = $CartItem->getProductClass();
                // FIXME 価格の設定箇所、ここでいいのか
                if ($ProductClass) {
                    $CartItem
                        ->setProductClass($ProductClass)
                        ->setPrice($ProductClass->getPrice02IncTax())
                    ;
                }
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('product');
        $resolver->setDefaults(array(
            'data_class' => CartItem::class,
            'id_add_product_id' => true,
            'constraints' => array(
                // FIXME new Assert\Callback(array($this, 'validate')),
            ),
        ));
    }

    /*
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['id_add_product_id']) {
            foreach ($view->vars['form']->children as $child) {
                $child->vars['id'] .= $options['product']->getId();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'add_cart';
    }

    /**
     * validate
     *
     * @param type             $data
     * @param ExecutionContext $context
     */
    public function validate($data, ExecutionContext $context)
    {
        $context->getValidator()->validate($data['product_class_id'], array(
            new Assert\NotBlank(),
        ), '[product_class_id]');
        if ($this->Product->getClassName1()) {
            $context->validateValue($data['classcategory_id1'], array(
                new Assert\NotBlank(),
                new Assert\NotEqualTo(array(
                    'value' => '__unselected',
                    'message' => 'form.type.select.notselect'
                )),
            ), '[classcategory_id1]');
        }
        //商品規格2初期状態(未選択)の場合の返却値は「NULL」で「__unselected」ではない
        if ($this->Product->getClassName2()) {
            $context->getValidator()->validate($data['classcategory_id2'], array(
                new Assert\NotBlank(),
                new Assert\NotEqualTo(array(
                    'value' => '__unselected',
                    'message' => 'form.type.select.notselect'
                )),
            ), '[classcategory_id2]');
        }
    }
}
