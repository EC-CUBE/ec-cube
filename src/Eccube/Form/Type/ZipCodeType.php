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

namespace Eccube\Form\Type;

use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @FormType
 */
class ZipCodeType extends AbstractType
{
    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;


    /**
     * @var \Eccube\Application $app
     * @Inject(Application::class)
     */
    protected $app;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // required の場合は NotBlank も追加する
        if ($options['required']) {
            $options['constraints'] = array_merge(array(
                new Assert\NotBlank(array()),
            ), $options['constraints']);
        }

        if (!isset($options['error_bubbling'])) {
            $options['error_bubbling'] = $options['error_bubbling'];
        }

        $builder->add('zip_code', TextType::class, $options);
        $builder->setAttribute('zip_cdoe', $options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['zip_code'] = $builder->getAttribute('zip_code');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'constraints' => array(
                new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
                new Assert\Length(array('min' => 3, 'max' => 7)
            ),
            'attr' => array('class' => 'p-postal-code')),
            'error_bubbling' => false,
            'inherit_data' => true,
            'trim' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'zip';
    }
}
