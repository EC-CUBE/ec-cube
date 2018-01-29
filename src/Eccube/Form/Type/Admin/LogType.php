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

use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LogType extends AbstractType
{
    /**
     * @var array
     */
    protected $appConfig;


    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * LogType constructor.
     * @param $eccubeConfig
     * @param KernelInterface $kernel
     */
    public function __construct($eccubeConfig, KernelInterface $kernel)
    {
        $this->appConfig = $eccubeConfig;
        $this->kernel = $kernel;
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $files = array();
        $finder = new Finder();
        $finder->name('*.log')->depth('== 0');
        foreach ($finder->in($this->kernel->getLogDir()) as $file) {
            $files[$file->getFilename()] = $file->getFilename();
        }

        $builder
            ->add('files', ChoiceType::class, array(
                'label' => 'ログファイル',
                'choices' => array_flip($files),
                'data' => 'site_'.date('Y-m-d').'.log',
                'expanded' => false,
                'multiple' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('line_max', TextType::class, array(
                'label' => '表示行数',
                'data' => '50',
                'constraints' => array(
                    new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
                    new Assert\NotBlank(),
                ),
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_system_log';
    }
}
