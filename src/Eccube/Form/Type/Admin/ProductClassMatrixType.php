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

use Doctrine\ORM\EntityRepository;
use Eccube\Entity\ClassName;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProductClassMatrixType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('class_name1', EntityType::class, [
                'class' => ClassName::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('cn')
                        ->orderBy('cn.sort_no', 'DESC');
                },
                'placeholder' => 'admin.product.select__class1',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('class_name2', EntityType::class, [
                'class' => ClassName::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('cn')
                        ->orderBy('cn.sort_no', 'DESC');
                },
                'placeholder' => 'admin.product.select__class2',
                'constraints' => new Callback(function (
                    ClassName $ClassName2 = null,
                    ExecutionContextInterface $context
                ) {
                    $ClassName1 = $context->getRoot()->get('class_name1')->getData();
                    if ($ClassName1 && $ClassName2) {
                        if ($ClassName1->getId() === $ClassName2->getId()) {
                            $context->buildViolation(trans('admin.product.select__can_not_select_same_class'))
                                ->atPath('class_name2')
                                ->addViolation();
                        }
                    }
                }),
                'required' => false,
            ])
            ->add('product_classes', CollectionType::class, [
                'entry_type' => ProductClassEditType::class,
                'allow_add' => true,
                'error_bubbling' => false,
            ])
            ->add('save', SubmitType::class);

        if ($options['product_classes_exist']) {
            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $ProductClasses = $form['product_classes']->getData();
                $hasVisible = false;
                foreach ($ProductClasses as $ProductClass) {
                    if ($ProductClass->isVisible()) {
                        $hasVisible = true;
                        break;
                    }
                }

                if (!$hasVisible) {
                    $form['product_classes']->addError(new FormError(trans('admin.product.unselected_class')));
                }
            });
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'product_classes_exist' => false,
        ]);
    }
}
