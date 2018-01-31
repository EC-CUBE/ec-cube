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

namespace Eccube\Form\Type;

use Eccube\Common\EccubeConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class KanaType extends AbstractType
{
    /**
     * @var \Eccube\Common\EccubeConfig
     */
    protected $appConfig;

    /**
     * KanaType constructor.
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
        // ひらがなをカタカナに変換する
        // 引数はmb_convert_kanaのもの
        $builder->addEventSubscriber(new \Eccube\Form\EventListener\ConvertKanaListener('CV'));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'lastname_options' => array(
                'attr' => array(
                    'placeholder' => 'Kana01',
                ),
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
                    )),
                    new Assert\Length(array(
                        'max' => $this->appConfig['kana_len'],
                    )),
                ),
            ),
            'firstname_options' => array(
                'attr' => array(
                    'placeholder' => 'Kana02',
                ),
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
                    )),
                    new Assert\Length(array(
                        'max' => $this->appConfig['kana_len'],
                    )),
                ),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return NameType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'kana';
    }
}
