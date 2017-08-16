<?php

namespace Eccube\Form\Type;

use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

class CartItemType extends AbstractType
{
    /**
     * @var \Eccube\Application $app
     * @Inject(Application::class)
     */
    protected $app;

    /** @var \Eccube\Entity\Product */
    protected $Product;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var $Product \Eccube\Entity\Product */
        $this->Product = $options['Product'];
        $Product = $this->Product;
        $ProductClasses = $Product->getProductClasses();

        $builder
            ->add('mode', HiddenType::class, [
                'data' => 'add_cart',
                'mapped' => false,
            ])
            ->add(
                $builder
                    ->create('object', HiddenType::class, [
                        'data' => $ProductClasses->count() === 1 ?
                            $ProductClasses->first() :
                            null,
                        'data_class' => null,
                        'constraints' => [
                            new Assert\NotBlank(),
                        ],
                    ])
                    ->addModelTransformer(new EntityToIdTransformer($this->app['orm.em'], 'Eccube\Entity\ProductClass'))
            )
            ->add('quantity', IntegerType::class, [
                'data' => 1,
                'attr' => [
                    'min' => 1,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThanOrEqual([
                        'value' => 1,
                    ]),
                    new Assert\Regex(['pattern' => '/^\d+$/']),
                ],
            ])
        ;

        if ($Product && $Product->getProductClasses()) {

            if (!is_null($Product->getClassName1())) {
                $builder->add('classcategory_id1', ChoiceType::class, [
                    'label' => $Product->getClassName1(),
                    'mapped' => false,
                    'choices' => ['選択してください' => '__unselected'] + $Product->getClassCategories1AsFlip(),
                ]);
            }

            if (!is_null($Product->getClassName2())) {
                $builder->add('classcategory_id2', ChoiceType::class, [
                    'label' => $Product->getClassName2(),
                    'mapped' => false,
                    'choices' => ['選択してください' => '__unselected'],
                ]);
            }
        }

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($Product) {

            $data = $event->getData();
            $form = $event->getForm();
            if (!is_null($Product->getClassName2())) {
                $classcategory_id1 = $data['classcategory_id1'];
                if ($classcategory_id1) {
                    $form->add('classcategory_id2', ChoiceType::class, [
                        'label' => $Product->getClassName2(),
                        'mapped' => false,
                        'choices' => ['選択してください' => '__unselected'] + $Product->getClassCategories2AsFlip($classcategory_id1),
                    ]);
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (is_object($view->vars['form']->children['object']->vars['value'])) {
            $view->vars['form']->children['object']->vars['value'] = $view->vars['form']->children['object']->vars['value']->getId();
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('Product');
        $resolver->setDefaults([
            'id_add_product_id' => true,
            'data_class' => 'Eccube\Entity\CartItem',
        ]);
    }

    /**
     * @param mixed $data
     * @param ExecutionContext $context
     */
    public function validate($data, ExecutionContext $context)
    {
        if ($data['mode'] !== 'add_favorite') {
            $context->getValidator()->validate($data['product_class_id'], array(
                new Assert\NotBlank(),
            ), '[product_class_id]');
            if ($this->Product->getClassName1()) {
                $context->validateValue($data['classcategory_id1'], array(
                    new Assert\NotBlank(),
                    new Assert\NotEqualTo(array(
                        'value' => '__unselected',
                        'message' => 'form.type.select.notselect'
                    )),
                ), '[classcategory_id1]');
            }
            //商品規格2初期状態(未選択)の場合の返却値は「NULL」で「__unselected」ではない
            if ($this->Product->getClassName2()) {
                $context->getValidator()->validate($data['classcategory_id2'], array(
                    new Assert\NotBlank(),
                    new Assert\NotEqualTo(array(
                        'value' => '__unselected',
                        'message' => 'form.type.select.notselect'
                    )),
                ), '[classcategory_id2]');
            }

        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cart_item';
    }
}
