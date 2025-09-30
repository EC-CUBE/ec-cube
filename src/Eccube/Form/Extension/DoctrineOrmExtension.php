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

use Doctrine\ORM\EntityManager;
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
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $config = $form->getConfig();
                // data_classオプションが必要
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

                /** @var \ReflectionProperty[] $props */
                $props = $meta->getReflectionProperties();
                foreach ($props as $prop) {
                    $attrs = $prop->getAttributes(FormAppend::class);
                    foreach ($attrs as $attr) {
                        $instance = $attr->newInstance();
                        if ($instance) {
                            $options = is_null($instance->options) ? [] : $instance->options;
                            $options['eccube_form_options'] = [
                                'auto_render' => (true === $instance->auto_render),
                                'form_theme' => $instance->form_theme,
                                'style_class' => $instance->style_class ?: 'ec-select',
                            ];
                            if (!isset($form[$prop->getName()])) {
                                $form->add($prop->getName(), $instance->type, $options);
                            }
                        }
                    }
                }
            }
        );
    }

    #[\Override]
    public function buildView(FormView $view, FormInterface $form, array $options)
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

    #[\Override]
    public function configureOptions(OptionsResolver $resolver)
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
