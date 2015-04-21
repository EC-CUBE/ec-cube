<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
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
            ->add('product_id', 'hidden', array(
                'data' => $Product->getId(),
            ))
            ->add('product_class_id', 'hidden')
            ->add('quantity', 'integer', array(
                'data' => 1,
                'attr' => array(
                    'min' => 1,
                    'maxlength' => $this->config['int_len'],
                ),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($Product) {
            $form = $event->getForm();

            if ($Product && $Product->getProductClasses()) {
                if ($Product->getClassName1()) {
                    $form->add('classcategory_id1', 'choice', array(
                        'label' => $Product->getClassName1(),
                        'choices'   => array('__unselected' => '選択してください') + $Product->getClassCategories1(),
                        'constraints' => array(
                            new Assert\NotBlank(),
                            new Assert\NotEqualTo(array(
                                'value' => '__unselected',
                                'message' => 'This value should be blank.',
                            )),
                        ),
                    ));
                }
                if ($Product->getClassName2()) {
                    $form->add('classcategory_id2', 'choice', array(
                        'label' => $Product->getClassName2(),
                        'choices' => array('__unselected' => '選択してください'),
                        'constraints' => array(
                            new Assert\NotBlank(),
                            new Assert\NotEqualTo(array(
                                'value' => '__unselected',
                                'message' => 'This value should be blank.',
                            )),
                        ),
                    ));
                }
            }
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($Product) {
            $data = $event->getData();
            $form = $event->getForm();
            if ($Product->getClassName2()) {
                if ($data['classcategory_id1']) {
                    $form->add('classcategory_id2', 'choice', array(
                        'label' => $Product->getClassName2(),
                        'choices' => array('__unselected' => '選択してください') + $Product->getClassCategories2($data['classcategory_id1']),
                        'constraints' => array(
                            new Assert\NotBlank(),
                            new Assert\NotEqualTo(array(
                                'value' => '__unselected',
                                'message' => 'This value should be blank.',
                            )),
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
        $resolver->setDefaults(array(
            'id_add_product_id' => true,
        ));
    }

    /*
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['id_add_product_id']) {
            foreach ($view->vars['form']->children as $child) {
                $child->vars['id'] .= $options['product']->getId();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'add_cart';
    }
}
