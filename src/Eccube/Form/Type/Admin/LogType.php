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

use Eccube\Common\EccubeConfig;
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
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * LogType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param KernelInterface $kernel
     */
    public function __construct(EccubeConfig $eccubeConfig, KernelInterface $kernel)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $files = [];
        $finder = new Finder();
        $finder->name('*.log')->depth('== 0');
        $dirs = $this->kernel->getLogDir().DIRECTORY_SEPARATOR.$this->kernel->getEnvironment();
        foreach ($finder->in($dirs) as $file) {
            $files[$file->getFilename()] = $file->getFilename();
        }

        $builder
            ->add('files', ChoiceType::class, [
                'label' => 'logtype.label.log_file',
                'choices' => array_flip($files),
                'data' => 'site_'.date('Y-m-d').'.log',
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('line_max', TextType::class, [
                'label' => 'logtype.label.number_of_rows',
                'data' => '50',
                'attr' => [
                    'maxlength' => 5,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range(['min' => 0, 'max' => 50000]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_system_log';
    }
}
