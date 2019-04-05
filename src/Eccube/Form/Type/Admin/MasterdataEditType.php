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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MasterdataEditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('data', CollectionType::class, [
                'entry_type' => MasterdataDataType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
            ->add('masterdata_name', HiddenType::class)
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                // IDのみを配列化
                $ids = [];
                foreach ($data['data'] as $key => $value) {
                    if (isset($value['id'])) {
                        $ids[$key] = $value['id'];
                    }
                }

                if (count($ids) > 0) {
                    // 重複IDチェック
                    $idCount = array_count_values($ids);
                    foreach ($idCount as $id => $count) {
                        // 同じIDの数が2つ以上の場合は重複
                        if ($count >= 2) {
                            $keys = array_keys($ids, $id);
                            // 重複した全ての入力項目にエラーを出力
                            foreach ($keys as $key) {
                                $form['data'][$key]['id']->addError(new FormError(trans('admin.setting.system.master_data.duplicate_id')));
                            }
                        }
                    }
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_system_masterdata_edit';
    }
}
