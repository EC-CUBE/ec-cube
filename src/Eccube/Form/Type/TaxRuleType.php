<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TaxRuleType extends AbstractType
{
    public $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tax_rate', 'integer', array(
                'label' => '消費税率',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Range(array('min' => 0, 'max' => 100))
                ),
            ))
            ->add('calc_rule', 'calc_rule', array(
                'label' => '課税規則',
                'required' => true,
            ))
            ->add('apply_date', 'date', array(
                'label' => '適用日時',
                'required' => 'false',
                'input' => 'datetime',
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd h:i',
                'years' => range(date('Y'), date('Y') + 2),
                'empty_value' => array(
                    'year' => '----',
                    'month' => '--',
                    'day' => '--',
                    'hours' => '--',
                    'minutes' => '--'
                ),
            ))
            ->add('save', 'submit', array('label' => 'この内容で登録する'));

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tax_rule';
    }
}
