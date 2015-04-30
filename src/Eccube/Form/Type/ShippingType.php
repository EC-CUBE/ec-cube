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

class ShippingType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Country');
        $builder->add('Pref');
        $builder->add('name01');
        $builder->add('name02');
        $builder->add('kana01');
        $builder->add('kana02');
        $builder->add('company_name');
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
        $builder->add('time_id');
        $builder->add('shipping_time');
        $builder->add('shipping_date');
        $builder->add('shipping_commit_date');
        $builder->add('ShipmentItems', 'collection', array('type' => new ShipmentItemType()));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Eccube\Entity\Shipping',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shipping';
    }
} 