<?php

namespace Eccube\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LoginType extends AbstractType
{
    public $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('login_id', 'text', array(
            'attr' => array(
                'max_length' => 50,
            ),
            'constraints' => array(
                new Assert\NotBlank(),
            ),
            'data' => $this->session->get('_security.last_username'),
        ));
        $builder->add('password', 'password', array(
            'attr' => array(
                'max_length' => 50,
            ),
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_login';
    }

}
