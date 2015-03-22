<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TelType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['tel01_options']['required'] = $options['required'];
        $options['tel02_options']['required'] = $options['required'];
        $options['tel03_options']['required'] = $options['required'];

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }

        if (empty($options['tel01_name'])) {
            $options['tel01_name'] = $builder->getName() . '01';
        }
        if (empty($options['tel02_name'])) {
            $options['tel02_name'] = $builder->getName() . '02';
        }
        if (empty($options['tel03_name'])) {
            $options['tel03_name'] = $builder->getName() . '03';
        }

        $builder
            ->add($options['tel01_name'], 'text', array_merge($options['options'], $options['tel01_options']))
            ->add($options['tel02_name'], 'text', array_merge($options['options'], $options['tel02_options']))
            ->add($options['tel03_name'], 'text', array_merge($options['options'], $options['tel03_options']))
        ;

        $builder->setAttribute('tel01_name', $options['tel01_name']);
        $builder->setAttribute('tel02_name', $options['tel02_name']);
        $builder->setAttribute('tel03_name', $options['tel03_name']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['tel01_name'] = $builder->getAttribute('tel01_name');
        $view->vars['tel02_name'] = $builder->getAttribute('tel02_name');
        $view->vars['tel03_name'] = $builder->getAttribute('tel03_name');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'options' => array(),
            'tel01_options' => array(
                'attr' => array(
                    'maxlength' => 3,
                ),
                'constraints' => array(
                    new Assert\Length(array('min' => 2, 'max' => 3)),
                    new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                ),
            ),
            'tel02_options' => array(
                'attr' => array(
                    'maxlength' => 4,
                ),
                'constraints' => array(
                    new Assert\Length(array('min' => 2, 'max' => 4)),
                    new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                ),
            ),
            'tel03_options' => array(
                'attr' => array(
                    'maxlength' => 4,
                ),
                'constraints' => array(
                    new Assert\Length(array('min' => 2, 'max' => 4)),
                    new Assert\Regex(array('pattern' => '/\A\d+\z/')),
                ),
            ),
            'tel01_name' => '',
            'tel02_name' => '',
            'tel03_name' => '',
            'error_bubbling' => false,
            'inherit_data' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tel';
    }
}
