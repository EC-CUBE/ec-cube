<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        $finder->name('*.log')
            ->depth('== 0')
            ->sort(function (\SplFileInfo $a, \SplFileInfo $b) {
                return strcmp($b->getMTime(), $a->getMTime());
            });
        $dirs = $this->kernel->getLogDir().DIRECTORY_SEPARATOR.$this->kernel->getEnvironment();
        foreach ($finder->in($dirs) as $file) {
            $files[$file->getFilename()] = $file->getFilename();
        }

        $builder
            ->add('files', ChoiceType::class, [
                'choices' => array_flip($files),
                'data' => 'site_'.date('Y-m-d').'.log',
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('line_max', TextType::class, [
                'data' => '50',
                'attr' => [
                    'maxlength' => 5,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range(['min' => 1, 'max' => 50000]),
                ],
            ])
            ->add('download', SubmitType::class, [
                'label' => 'admin.common.download',
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
