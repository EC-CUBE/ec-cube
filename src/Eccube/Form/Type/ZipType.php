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

namespace Eccube\Form\Type;

use Eccube\Common\EccubeConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ZipType
 */
class ZipType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * ZipType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['zip01_options']['required'] = $options['required'];
        $options['zip02_options']['required'] = $options['required'];

        // required の場合は NotBlank も追加する
        if ($options['required']) {
            $options['options']['constraints'] = array_merge([
                new Assert\NotBlank([]),
            ], $options['options']['constraints']);
        }

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }

        if (empty($options['zip01_name'])) {
            $options['zip01_name'] = $builder->getName().'01';
        }
        if (empty($options['zip02_name'])) {
            $options['zip02_name'] = $builder->getName().'02';
        }

        $builder
            ->add($options['zip01_name'], TextType::class, array_merge_recursive($options['options'], $options['zip01_options']))
            ->add($options['zip02_name'], TextType::class, array_merge_recursive($options['options'], $options['zip02_options']))
        ;

        $builder->setAttribute('zip01_name', $options['zip01_name']);
        $builder->setAttribute('zip02_name', $options['zip02_name']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['zip01_name'] = $builder->getAttribute('zip01_name');
        $view->vars['zip02_name'] = $builder->getAttribute('zip02_name');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'options' => ['constraints' => [], 'attr' => ['class' => 'p-postal-code']],
            'zip01_options' => [
                'attr' => [
                    'placeholder' => 'Zip01',
                ],
                'constraints' => [
                    new Assert\Type(['type' => 'numeric', 'message' => 'form.type.numeric.invalid']),
                    new Assert\Length(['min' => $this->eccubeConfig['eccube_zip01_len'], 'max' => $this->eccubeConfig['eccube_zip01_len']]),
                ],
            ],
            'zip02_options' => [
                'attr' => [
                    'placeholder' => 'Zip02',
                ],
                'constraints' => [
                    new Assert\Type(['type' => 'numeric', 'message' => 'form.type.numeric.invalid']),
                    new Assert\Length(['min' => $this->eccubeConfig['eccube_zip02_len'], 'max' => $this->eccubeConfig['eccube_zip02_len']]),
                ],
            ],
            'zip01_name' => '',
            'zip02_name' => '',
            'error_bubbling' => false,
            'inherit_data' => true,
            'trim' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'zip';
    }
}
