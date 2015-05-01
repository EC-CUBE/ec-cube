<?php

namespace Eccube\Form\Type\Master;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ZipType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['zip01_options']['required'] = $options['required'];
        $options['zip02_options']['required'] = $options['required'];

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }

        if (empty($options['zip01_name'])) {
            $options['zip01_name'] = $builder->getName() . '01';
        }
        if (empty($options['zip02_name'])) {
            $options['zip02_name'] = $builder->getName() . '02';
        }

        $builder
            ->add($options['zip01_name'], 'text', array_merge($options['options'], $options['zip01_options']))
            ->add($options['zip02_name'], 'text', array_merge($options['options'], $options['zip02_options']))
        ;

        $builder->setAttribute('zip01_name', $options['zip01_name']);
        $builder->setAttribute('zip02_name', $options['zip02_name']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['zip01_name'] = $builder->getAttribute('zip01_name');
        $view->vars['zip02_name'] = $builder->getAttribute('zip02_name');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'options' => array(),
            'zip01_options' => array(
                'attr' => array(
                    'maxlength' => 3,
                ),
                'constraints' => array(
                    new Assert\Length(array('min' => 3, 'max' => 3))
                ),
            ),
            'zip02_options' => array(
                'attr' => array(
                    'maxlength' => 4,
                ),
                'constraints' => array(
                    new Assert\Length(array('min' => 4, 'max' => 4))
                ),
            ),
            'zip01_name' => '',
            'zip02_name' => '',
            'error_bubbling' => false,
            'inherit_data' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zip';
    }
}
