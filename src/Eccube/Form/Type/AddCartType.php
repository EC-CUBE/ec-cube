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

class AddCartType extends AbstractType
{

    public $config;
    public $security;
    public $customerFavoriteProductRepository;
    public $Product = null;

    public function __construct(
        $config = null,
        \Eccube\Repository\CustomerFavoriteProductRepository $customerFavoriteProductRepository = null
    ) {
        $this->config = $config;
        $this->customerFavoriteProductRepository = $customerFavoriteProductRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var $Product \Eccube\Entity\Product */
        $Product = $options['product'];
        $this->Product = $Product;
        $ProductClasses = $Product->getProductClasses();

        $builder
            ->add('mode', HiddenType::class, array(
                'data' => 'add_cart',
            ))
            ->add('product_id', HiddenType::class, array(
                'data' => $Product->getId(),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Regex(array('pattern' => '/^\d+$/')),
                ),
            ))
            ->add('product_class_id', HiddenType::class, array(
                'data' => count($ProductClasses) === 1 ? $ProductClasses[0]->getId() : '',
                'constraints' => array(
                    new Assert\Regex(array('pattern' => '/^\d+$/')),
                ),
            ));

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
                    $builder->add('classcategory_id1', ChoiceType::class, array(
                        'label' => $Product->getClassName1(),
                        'choices'   => array_flip(array('選択してください' => '__unselected')) + $Product->getClassCategories1AsFlip(),
                    ));
                }
                if (!is_null($Product->getClassName2())) {
                    $builder->add('classcategory_id2', ChoiceType::class, array(
                        'label' => $Product->getClassName2(),
                        'choices' => array_flip(array('選択してください' => '__unselected')),
                    ));
                }
            }

            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($Product) {
                $data = $event->getData();
                $form = $event->getForm();
                if (!is_null($Product->getClassName2())) {
                    if ($data['classcategory_id1']) {
                        $form->add('classcategory_id2', ChoiceType::class, array(
                            'label' => $Product->getClassName2(),
                            'choices' => array_flip(array('__unselected' => '選択してください')) + $Product->getClassCategories2AsFlip($data['classcategory_id1']),
                        ));
                    }
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

        if ($view->vars['form']->children['mode']->vars['value'] === 'add_cart') {
            $view->vars['form']->children['mode']->vars['value'] = '';
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
        if ($data['mode'] !== 'add_favorite') {
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
}
