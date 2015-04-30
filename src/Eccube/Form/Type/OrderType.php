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

class OrderType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden')
            // ->add('Country')
            // ->add('zipcode', 'text')
            ->add('Deliv', 'entity', array(
                'class' => 'Eccube\Entity\Deliv',
                'property' => 'name',
                'expanded' => false,
                'multiple' => false,
                'empty_value' => '-',
            ))
            ->add('Payment', 'entity', array(
                'class' => 'Eccube\Entity\Payment',
                'property' => 'method',
                'expanded' => false,
                'multiple' => false,
                'empty_value' => '-',
            ))
            ->add('Customer')
            ->add('Sex', 'sex', array(
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('Job')
            ->add('DeviceType')
            ->add('message')
            ->add('name', 'name')
            ->add('kana01', 'text')
            ->add('kana02', 'text')
            ->add('company_name', 'text')
            ->add('email', 'text')
            ->add('tel', 'tel')
            ->add('fax', 'fax')
            ->add('zip', 'zip')
            ->add('address', 'address')
            ->add('birth', 'birthday', array(
                'format' => 'yyyy-MM-dd',
            ))
            ->add('subtotal')
            ->add('discount')
            ->add('deliv_fee')
            ->add('charge')
            ->add('use_point')
            ->add('add_point')
            ->add('birth_point')
            ->add('tax')
            ->add('total')
            ->add('payment_total')
            ->add('payment_method')
            ->add('note', 'textarea')
            ->add('OrderStatus')
            ->add('commit_date')
            ->add('payment_date')
            ->add('create_date')
            ->add('OrderDetails', 'collection', array('type' => new OrderDetailType()))
            ->add('Shippings', 'collection', array('type' => new ShippingType()))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Eccube\Entity\Order',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order';
    }
} 