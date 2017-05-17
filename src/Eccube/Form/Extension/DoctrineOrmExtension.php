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

namespace Eccube\Form\Extension;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DoctrineOrmExtension extends AbstractTypeExtension
{
    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
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
                } catch (\Exception $e) {
                    return;
                }
                // フィールドからフォームへ定義
                $names = $meta->getFieldNames();
                foreach ($names as $name) {
                    $mapping = $meta->getFieldMapping($name);
                    if (isset($mapping['options']['eccube_form_options'])) {
                        $options = $mapping['options']['eccube_form_options'];
                        if (isset($options['auto_render']) && true === $options['auto_render']) {
                            $fieldName = $mapping['fieldName'];
                            if (!isset($form[$fieldName])) {
                                $form->add(
                                    $mapping['fieldName'],
                                    null,
                                    [
                                        'eccube_form_options' => $options,
                                    ]
                                );
                            }
                        }
                    }
                }
                // TODO Assosiationも対応する
            }
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $options = $form->getConfig()->getOption('eccube_form_options');
        $view->vars['eccube_form_options'] = $options;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault(
            'eccube_form_options',
            [
                'auto_render' => false,
                'form_theme' => null,
            ]
        );
    }

    public function getExtendedType()
    {
        return FormType::class;
    }
}
