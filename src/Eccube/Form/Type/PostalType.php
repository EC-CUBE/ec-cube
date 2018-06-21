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
class PostalType extends AbstractType
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
        $builder->addEventSubscriber(new \Eccube\Form\EventListener\ConvertZenToHanListener('as'));
        $builder->addEventSubscriber(new \Eccube\Form\EventListener\ConvertPostalListener());

        // required の場合は NotBlank も追加する
        if ($options['required']) {
            $options['options']['constraints'] = array_merge([
                new Assert\NotBlank([]),
            ], $options['options']['constraints']);
        }

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
//        $view->vars['zip01_name'] = $builder->getAttribute('zip01_name');
//        $view->vars['zip02_name'] = $builder->getAttribute('zip02_name');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'options' => [
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_postal_code']]),
                ],
            ],
            'attr' => [
                'class' => 'p-postal-code',
                'placeholder' => 'Postal',
            ],

//            'error_bubbling' => false,
            'trim' => true,
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'postal';
    }
}
