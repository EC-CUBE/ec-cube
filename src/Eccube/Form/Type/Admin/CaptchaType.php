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

namespace Eccube\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CaptchaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('captcha', TextType::class, [
            'label' => null,
            'constraints' => [
                new Assert\Regex(['pattern' => '/^[0-9a-zA-Z]+$/']),
                new Assert\NotBlank(),
            ],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'admin_auth_captcha';
    }
}
