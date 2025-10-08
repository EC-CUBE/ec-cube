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

namespace Eccube\Form\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Attribute\FormAppend;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @FormExtension
 */
class DoctrineOrmExtension extends AbstractTypeExtension
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array<mixed> $options
     *
     * @return void
     */
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $config = $form->getConfig();
                // data_classオプションが必要
                /** @var class-string|null $class */
                $class = $config->getDataClass();
                if (is_null($class)) {
                    return;
                }
                // メタデータの取得
                try {
                    $meta = $this->em->getClassMetadata($class);
                } catch (\Exception) {
                    return;
                }

                /** @var array<string, \Doctrine\ORM\Mapping\PropertyAccessor> $accessors */
                $accessors = $meta->getPropertyAccessors();
                foreach ($accessors as $propName => $accessor) {
                    $prop = $accessor->getUnderlyingReflector();
                    $attrs = $prop->getAttributes(FormAppend::class);
                    foreach ($attrs as $attr) {
                        $instance = $attr->newInstance();
                        $options = empty($instance->options) ? [] : $instance->options;
                        $options['eccube_form_options'] = [
                            'auto_render' => (true === $instance->auto_render),
                            'form_theme' => $instance->form_theme,
                            'style_class' => $instance->style_class ?: 'ec-select',
                        ];
                        if (!isset($form[$propName])) {
                            $form->add($propName, $instance->type, $options);
                        }
                    }
                }
            }
        );
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array<mixed> $options
     *
     * @return void
     */
    #[\Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $options = $form->getConfig()->getOption('eccube_form_options');

        if (!array_key_exists('auto_render', $options)) {
            $options['auto_render'] = false;
        }

        if (!array_key_exists('form_theme', $options)) {
            $options['form_theme'] = null;
        }

        if (!array_key_exists('style_class', $options)) {
            $options['style_class'] = 'ec-select';
        }

        $view->vars['eccube_form_options'] = $options;
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault(
            'eccube_form_options',
            [
                'auto_render' => false,
                'form_theme' => null,
                'style_class' => 'ec-select',
            ]
        );
    }

    /**
     * Return the class of the type being extended.
     */
    #[\Override]
    public static function getExtendedTypes(): iterable
    {
        // return FormType::class to modify (nearly) every field in the system
        return [FormType::class];
    }
}
