<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class NameType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['lastname_options']['required'] = $options['required'];
        $options['firstname_options']['required'] = $options['required'];

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }

        if (empty($options['lastname_name'])) {
            $options['lastname_name'] = $builder->getName() . '01';
        }
        if (empty($options['firstname_name'])) {
            $options['firstname_name'] = $builder->getName() . '02';
        }

        $builder
            ->add($options['lastname_name'], 'text', array_merge($options['options'], $options['lastname_options']))
            ->add($options['firstname_name'], 'text', array_merge($options['options'], $options['firstname_options']))
        ;

        $builder->setAttribute('lastname_name', $options['lastname_name']);
        $builder->setAttribute('firstname_name', $options['firstname_name']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['lastname_name'] = $builder->getAttribute('lastname_name');
        $view->vars['firstname_name'] = $builder->getAttribute('firstname_name');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'options' => array(),
            'lastname_options' => array(),
            'firstname_options' => array(),
            'lastname_name' => '',
            'firstname_name' => '',
            'error_bubbling' => false,
            'inherit_data' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'name';
    }
}
