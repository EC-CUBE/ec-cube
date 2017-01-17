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


namespace Eccube\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class PluginManagementType extends AbstractType
{

    public function __construct()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $plugin_id = $options['plugin_id'];

        $builder
            ->add('plugin_id', HiddenType::class, array(
                'data' => $plugin_id,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('plugin_archive', FileType::class, array(
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => array(
                    new Assert\NotBlank(array('message' => 'ファイルを選択してください。')),
                    new Assert\File(array(
                        'mimeTypes' => array('application/zip', 'application/x-tar', 'application/x-gzip'),
                        'mimeTypesMessage' => 'zipファイル、tarファイル、tar.gzファイルのいずれかをアップロードしてください。',
                    )),
                ),
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'plugin_management';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array('plugin_id'));
    }

}
