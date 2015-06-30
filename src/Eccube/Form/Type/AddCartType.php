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
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

class AddCartType extends AbstractType
{

    public $config;
    public $security;
    public $customerFavoriteProductRepository;
    public $Product = null;

    public function __construct(
        $config,
        \Symfony\Component\Security\Core\SecurityContext $security,
        \Eccube\Repository\CustomerFavoriteProductRepository $customerFavoriteProductRepository
    ) {
        $this->config = $config;
        $this->security = $security;
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
            ->add('mode', 'hidden', array(
                'data' => 'add_cart',
            ))
            ->add('product_id', 'hidden', array(
                'data' => $Product->getId(),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Regex(array('pattern' => '/^\d+$/')),
                ),
            ))
            ->add('product_class_id', 'hidden', array(
                'data' => count($ProductClasses) === 1 ? $ProductClasses[0]->getId() : '',
                'constraints' => array(
                    new Assert\Regex(array('pattern' => '/^\d+$/')),
                ),
            ))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());

        if ($Product->getStockFind()) {
            $builder
                ->add('quantity', 'integer', array(
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
                if ($Product->getClassName1()) {
                    $builder->add('classcategory_id1', 'choice', array(
                        'label' => $Product->getClassName1(),
                        'choices'   => array('__unselected' => '選択してください') + $Product->getClassCategories1(),
                    ));
                }
                if ($Product->getClassName2()) {
                    $builder->add('classcategory_id2', 'choice', array(
                        'label' => $Product->getClassName2(),
                        'choices' => array('__unselected' => '選択してください'),
                    ));
                }
            }

            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($Product) {
                $data = $event->getData();
                $form = $event->getForm();
                if ($Product->getClassName2()) {
                    if ($data['classcategory_id1']) {
                        $form->add('classcategory_id2', 'choice', array(
                            'label' => $Product->getClassName2(),
                            'choices' => array('__unselected' => '選択してください') + $Product->getClassCategories2($data['classcategory_id1']),
                        ));
                    }
                }
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired('product');
        $resolver->setDefaults(array(
            'id_add_product_id' => true,
            'constraints' => array(
                new Assert\Callback(array($this, 'validate')),
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
    public function getName()
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
        if ($data['mode'] === 'add_favorite') {
            if (!$this->security->isGranted('ROLE_USER')) {
                $context->addViolationAt('', 'ログインしてください.');
            }
        } else {
            $context->validateValue($data['product_class_id'], array(
                new Assert\NotBlank(),
            ), '[product_class_id]');
            if ($this->Product->getClassName1()) {
                $context->validateValue($data['classcategory_id1'], array(
                    new Assert\NotBlank(),
                    new Assert\NotEqualTo(array(
                        'value' => '__unselected',
                        'message' => 'This value should be blank.',
                    )),
                ), '[classcategory_id1]');
            }
            if ($this->Product->getClassName2()) {
                $context->validateValue($data['classcategory_id2'], array(
                    new Assert\NotBlank(),
                    new Assert\NotEqualTo(array(
                        'value' => '__unselected',
                        'message' => 'This value should be blank.',
                    )),
                ), '[classcategory_id2]');
            }
        }
    }
}
