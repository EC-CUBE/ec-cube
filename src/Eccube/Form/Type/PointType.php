<?php

namespace Eccube\Form\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;

class PointType extends AbstractType
{
    public $app;

    public function __construct (\Eccube\Application $app)
    {
        $this->app = $app;
    }

    public function getName()
    {
        return 'point';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('point_rate', 'integer', array(
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Range(array('min' => 0, 'max' => 100))),
            ))
            ->add('welcome_point', 'integer', array(
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Range(array('min' => 0, 'max' => 999999999))),
            ))
            ->add('save', 'submit', array('label' => 'この内容で登録する'));
    }
}
