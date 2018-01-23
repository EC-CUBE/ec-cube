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

use Eccube\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityType extends AbstractType
{
    /**
     * @var array
     */
    protected $appConfig;

    /**
     * @var RecursiveValidator
     */
    protected $recursiveValidator;

    /**
     * @Inject(Application::class)
     */
    protected $app;

    /**
     * SecurityType constructor.
     * @param array $eccubeConfig
     * @param ValidatorInterface $validator
     */
    public function __construct(array $eccubeConfig, ValidatorInterface $validator)
    {
        $this->appConfig = $eccubeConfig;
        $this->recursiveValidator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('admin_route_dir', TextType::class, array(
                'label' => 'ディレクトリ名',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('max' => $this->appConfig['stext_len'])),
                    new Assert\Regex(array(
                       'pattern' => "/^[0-9a-zA-Z]+$/",
                   )),
                ),
            ))
            ->add('admin_allow_hosts', TextareaType::class, array(
                'required' => false,
                'label' => 'IP制限',
                'constraints' => array(
                    new Assert\Length(array('max' => $this->appConfig['stext_len'])),
                ),
            ))
            ->add('force_ssl', CheckboxType::class, array(
                'label' => 'SSLを強制',
                'required' => false,
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();

                $ips = preg_split("/\R/", $data['admin_allow_hosts'], null, PREG_SPLIT_NO_EMPTY);

                foreach($ips as $ip) {
                    $errors = $this->recursiveValidator->validate($ip, array(
                            new Assert\Ip(),
                        )
                    );
                    if ($errors->count() != 0) {
                        $form['admin_allow_hosts']->addError(new FormError($ip . 'はIPv4アドレスではありません。'));
                    }
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_security';
    }
}
