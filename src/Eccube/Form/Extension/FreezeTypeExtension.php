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

namespace Eccube\Form\Extension;

use Eccube\Annotation\FormExtension;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @FormExtension
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'freeze' => false,
            'freeze_display_text' => true,
        ]);
    }

    public function getExtendedType()
    {
        return FormType::class;
    }
}
