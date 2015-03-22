<?php

namespace Eccube\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * FreezeTypeExtension.
 */
class FreezeTypeExtension extends AbstractTypeExtension
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('freeze', $options['freeze']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $freeze = $form->getConfig()->getAttribute('freeze');

        $target_form = $form;
        while ($target_form->getParent() && !$freeze) {
            $target_form = $target_form->getParent();
            $freeze = $target_form->getConfig()->getAttribute('freeze');
        }
        if ($freeze) {
            $view->vars['required'] = false;
            unset($view->vars['attr']['placeholder']);
        }

        $view->vars['freeze'] = $freeze;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'freeze' => false,
        ));
    }

    public function getExtendedType()
    {
        return 'form';
    }

}
