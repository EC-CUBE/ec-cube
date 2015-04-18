<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SexType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['sex_options']['required'] = $options['required'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Eccube\Entity\Master\Sex',
            'property' => 'name',
            'label' => false,
            'expanded' => true,
            'empty_value' => false,
        ));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'sex';
    }
}
