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
        $builder->setAttribute('freeze_display_text', $options['freeze_display_text']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $freeze = $form->getConfig()->getAttribute('freeze');
        $freeze_display_text = $form->getConfig()->getAttribute('freeze_display_text');

        $target_form = $form;
        while ($target_form->getParent()) {
            $target_form = $target_form->getParent();
            if (!$freeze) {
                $freeze = $target_form->getConfig()->getAttribute('freeze');
            }
            if ($freeze_display_text) {
                $freeze_display_text = $target_form->getConfig()->getAttribute('freeze_display_text');
            }
        }
        if ($freeze) {
            $view->vars['required'] = false;
            unset($view->vars['attr']['placeholder']);
        }

        $view->vars['freeze'] = $freeze;
        $view->vars['freeze_display_text'] = $freeze_display_text;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'freeze' => false,
            'freeze_display_text' => true,
        ));
    }

    public function getExtendedType()
    {
        return 'form';
    }

}
