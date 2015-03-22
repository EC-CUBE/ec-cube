<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CustomerLoginType extends AbstractType
{

    public function getName()
    {
        return 'customer_login';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('login_email', 'text', array(
            'max_length' => 50,
            'constraints' => array(
                new Assert\NotBlank(),
            )
        ));
        $builder->add('login_memory', 'checkbox', array(
            'required' => false,
            'label' => 'メールアドレスをコンピューターに記憶させる',
        ));
        $builder->add('login_pass', 'password', array(
            'max_length' => 50,
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

}
