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
        $builder->add('id', 'hidden');
        $builder->add('Customer');
        $builder->add('Country');
        $builder->add('Pref');
        $builder->add('Sex');
        $builder->add('Job');
//        $builder->add('Deliv');
//        $builder->add('Payment');
        $builder->add('DeviceType');
        $builder->add('message', 'text');
        $builder->add('name01', 'text');
        $builder->add('name02', 'text');
        $builder->add('kana01', 'text');
        $builder->add('kana02', 'text');
        $builder->add('company_name', 'text');
        $builder->add('email', 'text');
        $builder->add('tel01', 'text');
        $builder->add('tel02', 'text');
        $builder->add('tel03', 'text');
        $builder->add('fax01', 'text');
        $builder->add('fax02', 'text');
        $builder->add('fax03', 'text');
        $builder->add('zip01', 'text');
        $builder->add('zip02', 'text');
        $builder->add('zipcode', 'text');
        $builder->add('addr01', 'text');
        $builder->add('addr02', 'text');
        $builder->add('birth');
        $builder->add('subtotal');
        $builder->add('discount');
        $builder->add('deliv_fee');
        $builder->add('charge');
        $builder->add('use_point');
        $builder->add('add_point');
        $builder->add('birth_point');
        $builder->add('tax');
        $builder->add('total');
        $builder->add('payment_total');
        $builder->add('payment_method');
        $builder->add('note');
        $builder->add('OrderStatus');
        $builder->add('commit_date');
        $builder->add('payment_date');
        $builder->add('create_date');
        $builder->add('OrderDetails', 'collection', array('type' => new OrderDetailType()));
        $builder->add('Shippings', 'collection', array('type' => new ShippingType()));
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