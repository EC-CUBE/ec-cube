<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LogType extends AbstractType
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $config = $this->config;

        $files = array();
        $finder = new Finder();
        $finder->name('*.log')->depth('== 0');

        foreach ($finder->in($config['root_dir'].'/app/log/') as $file) {
            $files[$file->getFilename()] = $file->getFilename();
        }

        $builder
            ->add('files', 'choice', array(
                'label' => 'ログファイル',
                'choices' => $files,
                'data' => 'site_'.date('Y-m-d').'.log',
                'expanded' => false,
                'multiple' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('line_max', 'text', array(
                'label' => '表示行数',
                'data' => '50',
                'attr' => array(
                    'maxlength' => 5,
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Range(array('min' => 0, 'max' => 50000)),
                ),
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_system_log';
    }
}
