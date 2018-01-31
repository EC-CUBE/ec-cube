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

use Eccube\Common\EccubeConfig;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\KanaType;
use Eccube\Form\Type\NameType;
use Eccube\Form\Type\RepeatedEmailType;
use Eccube\Form\Type\TelType;
use Eccube\Form\Type\ZipType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class NonMemberType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $appConfig;

    /**
     * NonMemberType constructor.
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->appConfig = $eccubeConfig;
    }


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', NameType::class, array(
                'required' => true,
            ))
            ->add('kana', KanaType::class, array(
                'required' => true,
            ))
            ->add('company_name', TextType::class, array(
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->appConfig['stext_len'],
                    )),
                ),
            ))
            ->add('zip', ZipType::class, array(
                'required' => true,
            ))
            ->add('address', AddressType::class, array(
                'required' => true,
            ))
            ->add('tel', TelType::class, array(
                'required' => true,
            ))
            ->add('email', RepeatedEmailType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'nonmember';
    }
}
