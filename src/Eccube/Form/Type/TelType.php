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
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TelType
 */
class TelType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * TelType constructor.
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
        $options['tel01_options']['required'] = $options['required'];
        $options['tel02_options']['required'] = $options['required'];
        $options['tel03_options']['required'] = $options['required'];
        // required の場合は NotBlank も追加する
        if ($options['required']) {
            $options['options']['constraints'] = array_merge([
                new Assert\NotBlank([]),
            ], $options['options']['constraints']);
        }

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }
        // nameは呼び出しもとで定義したものを使う
        if (empty($options['tel01_name'])) {
            $options['tel01_name'] = $builder->getName().'01';
        }
        if (empty($options['tel02_name'])) {
            $options['tel02_name'] = $builder->getName().'02';
        }
        if (empty($options['tel03_name'])) {
            $options['tel03_name'] = $builder->getName().'03';
        }
        // 全角英数を事前に半角にする
        $builder->addEventSubscriber(new \Eccube\Form\EventListener\ConvertKanaListener());
        $builder
            ->add($options['tel01_name'], TextType::class, array_merge_recursive($options['options'], $options['tel01_options']))
            ->add($options['tel02_name'], TextType::class, array_merge_recursive($options['options'], $options['tel02_options']))
            ->add($options['tel03_name'], TextType::class, array_merge_recursive($options['options'], $options['tel03_options']))
        ;
        $builder->setAttribute('tel01_name', $options['tel01_name']);
        $builder->setAttribute('tel02_name', $options['tel02_name']);
        $builder->setAttribute('tel03_name', $options['tel03_name']);
        // todo 変
        $builder->addEventListener(FormEvents::POST_SUBMIT, function ($event) use ($options) {
            $form = $event->getForm();
            $count = 0;
            if ($form[$options['tel01_name']]->getData() != '') {
                $count++;
            }
            if ($form[$options['tel02_name']]->getData() != '') {
                $count++;
            }
            if ($form[$options['tel03_name']]->getData() != '') {
                $count++;
            }
            if ($count != 0 && $count != 3) {
                // todo メッセージをymlに入れる
                $form[$options['tel01_name']]->addError(new FormError('全て入力してください。'));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $builder = $form->getConfig();
        $view->vars['tel01_name'] = $builder->getAttribute('tel01_name');
        $view->vars['tel02_name'] = $builder->getAttribute('tel02_name');
        $view->vars['tel03_name'] = $builder->getAttribute('tel03_name');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'options' => ['constraints' => []],
            'tel01_options' => [
                'constraints' => [
                    new Assert\Type(['type' => 'numeric', 'message' => 'form.type.numeric.invalid']), //todo  messageは汎用的に出来ないものか?
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_tel_len'], 'min' => $this->eccubeConfig['eccube_tel_len_min']]),
                ],
            ],
            'tel02_options' => [
                'constraints' => [
                    new Assert\Type(['type' => 'numeric', 'message' => 'form.type.numeric.invalid']),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_tel_len'], 'min' => $this->eccubeConfig['eccube_tel_len_min']]),
                ],
            ],
            'tel03_options' => [
                'constraints' => [
                    new Assert\Type(['type' => 'numeric', 'message' => 'form.type.numeric.invalid']),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_tel_len'], 'min' => $this->eccubeConfig['eccube_tel_len_min']]),
                ],
            ],
            'tel01_name' => '',
            'tel02_name' => '',
            'tel03_name' => '',
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
        return 'tel';
    }
}
