<?php
/**
 * Created by PhpStorm.
 * User: lqdung
 * Date: 8/14/2018
 * Time: 2:07 PM
 */

namespace Eccube\Form\Type\Admin;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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

    public function configureOptions(OptionsResolver $resolver)
    {
    }

    public function getBlockPrefix()
    {
        return 'admin_auth_captcha';
    }
}
