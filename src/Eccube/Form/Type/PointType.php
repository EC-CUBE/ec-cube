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
        return 'adminbasispoint';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$app = $this->app;
        //$objFormParam->addParam('ポイント付与率', 'point_rate', PERCENTAGE_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        //$objFormParam->addParam('会員登録時付与ポイント', 'welcome_point', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        
        $builder
            ->add('point_rate', 'integer', array('constraints' => array(
                new Assert\NotBlank(),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 100, // PERCENTAGE_LEN
                )),
                'required' => true,
            )))
            ->add('welcome_point', 'integer', array('constraints' => array(
                new Assert\NotBlank(),
                new Assert\Range(array(
                    'min' => 0,
                    'max' => 999999999, // INT_LEN
                )),
                'required' => true,
            )))
        ;
    }
}
