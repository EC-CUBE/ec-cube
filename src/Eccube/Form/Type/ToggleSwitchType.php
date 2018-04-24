<?php

namespace Eccube\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ToggleSwitchType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['label_on'] = $options['label_on'];
        $view->vars['label_off'] = $options['label_off'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'label_on' => 'common.label.enabled',
            'label_off' => 'common.label.disabled',
        ]);
    }

    public function getParent()
    {
        return CheckboxType::class;
    }
}
