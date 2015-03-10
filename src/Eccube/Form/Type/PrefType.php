<?php

namespace Eccube\Form\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\Form\FormBuilder;
use \Symfony\Component\Form\FormBuilderInterface;
use \Symfony\Component\Validator\Constraints as Assert;
use \Symfony\Component\Validator\ExecutionContextInterface;

class PrefType extends AbstractType
{
    public $app;

    public function __construct (\Eccube\Application $app)
    {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;
        $builder->add('pref', 'entity', array(
            'class' => 'Eccube\Entity\Pref',
            'property' => 'name',
        ));
    }

    public function getName()
    {
        return 'pref';
    }
}