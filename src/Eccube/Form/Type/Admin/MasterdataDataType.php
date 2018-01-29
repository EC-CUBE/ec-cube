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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class MasterdataDataType
 * @package Eccube\Form\Type\Admin
 */
class MasterdataDataType extends AbstractType
{
    /**
     * @var array
     */
    protected $appConfig;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * MasterdataDataType constructor.
     * @param array $eccubeConfig
     * @param TranslatorInterface $translator
     */
    public function __construct(array $eccubeConfig, TranslatorInterface $translator)
    {
        $this->appConfig = $eccubeConfig;
        $this->translator = $translator;
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $trans = $this->translator;
        $builder
            ->add('id', TextType::class, array(
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->appConfig['int_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^\d+$/u',
                        'message' => $trans->trans('form.type.numeric.invalid'),
                    )),
                ),
            ))
            ->add('name', TextType::class, array(
                'required' => false,
            ))
        ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($trans) {
            $form = $event->getForm();
            $data = $form->getData();
            if (strlen($data['id']) && strlen($data['name']) == 0) {
                $form['name']->addError(new FormError($trans->trans('This value should not be blank.')));
            }

            if (strlen($data['name']) && strlen($data['id']) == 0) {
                $form['id']->addError(new FormError($trans->trans('This value should not be blank.')));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_system_masterdata_data';
    }
}
