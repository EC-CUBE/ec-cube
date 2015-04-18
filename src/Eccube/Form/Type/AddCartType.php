<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AddCartType extends AbstractType
{

    public $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var $Product \Eccube\Entity\Product */
        $Product = $options['product'];

        $builder
            ->add('product_class_id', 'hidden')
            ->add('quantity', 'integer', array(
                'attr' => array(
                    'maxlength' => $this->config['int_len'],
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($Product) {
            $builder = $event->getForm();

            if ($Product && $Product->getProductClasses()) {
                if ($Product->getClassName1()) {
                    $builder->add('classcategory_id1', 'choice', array(
                        'label' => $Product->getClassName1(),
                        'empty_value' => '選択してください',
                        'choices'   => $Product->getClassCategories1(),
                        'constraints' => array(
                            new Assert\NotBlank(),
                        ),
                    ));
                }
                if ($Product->getClassName2()) {
                    $builder->add('classcategory_id2', 'choice', array(
                        'label' => $Product->getClassName2(),
                        'empty_value' => '選択してください',
                        'choices' => array(),
                        'constraints' => array(
                            new Assert\NotBlank(),
                        ),
                    ));
                }
            }
        });
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($Product) {
            $builder = $event->getForm();
            if ($Product->getClassName2()) {
                $classcategory_id1 = $builder->get('classcategory_id1')->getData();
                if ($classcategory_id1) {
                    $builder->add('classcategory_id2', 'choice', array(
                        'label' => $Product->getClassName2(),
                        'empty_value' => '選択してください',
                        'choices' => $Product->getClassCategories2($classcategory_id1),
                        'constraints' => array(
                            new Assert\NotBlank(),
                        ),
                    ));
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired('product');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'add_cart';
    }
}
