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
        $builder->add('message');
        $builder->add('name01');
        $builder->add('name02');
        $builder->add('kana01');
        $builder->add('kana02');
        $builder->add('company_name');
        $builder->add('email');
        $builder->add('tel01');
        $builder->add('tel02');
        $builder->add('tel03');
        $builder->add('fax01');
        $builder->add('fax02');
        $builder->add('fax03');
        $builder->add('zip01');
        $builder->add('zip02');
        $builder->add('zipcode');
        $builder->add('addr01');
        $builder->add('addr02');
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
//        $builder->add('Status');
        $builder->add('commit_date');
        $builder->add('payment_date');
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