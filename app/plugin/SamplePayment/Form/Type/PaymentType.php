<?php

namespace Plugin\SamplePayment\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentType extends AbstractType
{
    public $app;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', 'text', array(
                'constraints' => array(
                    new Assert\NotNull(),
                )
            ))
            ->add('name01', 'text', array(
                'constraints' => array(
                    new Assert\NotNull(),
                )
            ))
            ->add('name02', 'text', array(
                'constraints' => array(
                    new Assert\NotNull(),
                )
            ))
            ->add('mm', 'integer', array(
                'constraints' => array(
                    new Assert\NotNull(),
                )
            ))
            ->add('yy', 'integer', array(
                'constraints' => array(
                    new Assert\NotNull(),
                )
            ))
            ->add('method', 'choice', array(
                'choices' => array(
                    '1' => '一括払い'
                ),
                'expanded' => false,
                'multiple' => false,
                'constraints' => array(
                    new Assert\NotNull(),
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sample_payment';
    }
}
