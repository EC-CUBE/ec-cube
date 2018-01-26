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


namespace Eccube\Form\Type\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints as Assert;

class CustomerLoginType extends AbstractType
{

    /**
     * @var array
     */
    protected $appConfig;

    /**
     * @var AuthenticationUtils
     */
    protected $authenticationUtils;

    public function __construct(AuthenticationUtils $authenticationUtils, array $eccubeConfig)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->appConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('login_email', EmailType::class, array(
            'attr' => array(
                'max_length' => $this->appConfig['stext_len'],
            ),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Email(array('strict' => true)),
            ),
            'data' => $this->authenticationUtils->getLastUsername(),
        ));
        $builder->add('login_memory', CheckboxType::class, array(
            'required' => false,
        ));
        $builder->add('login_pass', PasswordType::class, array(
            'attr' => array(
                'max_length' => $this->appConfig['stext_len'],
            ),
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'customer_login';
    }
}
