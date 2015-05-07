<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class DelivType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => '配送業者名',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank,
                ),
            ))
            ->add('service_name', 'text', array(
                'label' => '名称',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('remark', 'textarea', array(
                'label' => '説明',
            ))
            ->add('confirm_url', 'text', array(
                'label' => '伝票No.URL',
                'constraints' => array(
                    new Assert\Url(),
                ),
            ))
            ->add('product_type', 'product_type', array(
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('payments', 'entity', array(
                'label' => '支払方法',
                'class' => 'Eccube\Entity\Payment',
                'property' => 'method',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.rank', 'ASC');
                },
                'mapped' => false,
            ))
            ->add('deliv_times', 'collection', array(
                'label' => 'お届け時間',
                'required' => false,
                'type' => 'deliv_time',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ))
            ->add('deliv_fees', 'collection', array(
                'label' => '都道府県別設定',
                'required' => true,
                'type' => 'deliv_fee',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Deliv',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'deliv';
    }
}
