<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AddressType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['pref_options']['required'] = $options['required'];
        $options['addr01_options']['required'] = $options['required'];
        $options['addr02_options']['required'] = $options['required'];

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }

        $builder
            ->add($options['pref_name'], 'pref', array_merge($options['options'], $options['pref_options']))
            ->add($options['addr01_name'], 'text', array_merge($options['options'], $options['addr01_options']))
            ->add($options['addr02_name'], 'text', array_merge($options['options'], $options['addr02_options']))
        ;

        $builder->setAttribute('pref_name', $options['pref_name']);
        $builder->setAttribute('addr01_name', $options['addr01_name']);
        $builder->setAttribute('addr02_name', $options['addr02_name']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['pref_name'] = $builder->getAttribute('pref_name');
        $view->vars['addr01_name'] = $builder->getAttribute('addr01_name');
        $view->vars['addr02_name'] = $builder->getAttribute('addr02_name');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'options' => array(),
            'pref_options' => array(),
            'addr01_options' => array(),
            'addr02_options' => array(),
            'pref_name' => 'pref',
            'addr01_name' => 'addr01',
            'addr02_name' => 'addr02',
            'error_bubbling' => false,
            'inherit_data' => true,
        ));
    }

    public function getName()
    {
        return 'address';
    }
}
