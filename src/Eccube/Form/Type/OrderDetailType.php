<?php
/**
 * Created by PhpStorm.
 * User: chihiro_adachi
 * Date: 15/04/23
 * Time: 15:17
 */

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class OrderDetailType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            // ->add('ProductClass', 'entity', array(
            //     'label' => '原因',
            //     'class' => 'Eccube\Entity\ProductClass',
            //     'property' => 'code',
            // ))
            ->add('product_name')
            ->add('product_code')
            ->add('classcategory_name1')
            ->add('classcategory_name2')
            ->add('price')
            ->add('quantity')
            ->add('point_rate')
            ->add('tax_rate')
            ->add('tax_rule')
            ->add('total_price');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Eccube\Entity\OrderDetail',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order_detail';
    }
} 