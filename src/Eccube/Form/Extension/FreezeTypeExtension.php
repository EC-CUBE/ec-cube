<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


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
