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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ShipmentItemType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('shipping_id');
        $builder->add('order_id');
        $builder->add('product_class_id');
        $builder->add('product_name');
        $builder->add('product_code');
        $builder->add('classcategory_name1');
        $builder->add('classcategory_name2');
        $builder->add('price');
        $builder->add('quantity');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Eccube\Entity\ShipmentItem',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shipment_item';
    }
}
