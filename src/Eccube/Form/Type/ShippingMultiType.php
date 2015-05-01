<?php
/**
 * Created by PhpStorm.
 * User: chihiro_adachi
 * Date: 15/04/18
 * Time: 19:50
 */

namespace Eccube\Form\Type;

use Eccube\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ShippingMultiType extends AbstractType
{
    private $app;
    private $config;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->config = $app['config'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $Customer = $this->app['user'];

        $builder
            ->add('product_class_id', 'hidden')
            ->add('quantity', 'integer', array(
                    'data' => 1,
                    'attr' => array(
                        'min' => 0,
                        'maxlength' => 100 // $this->config['int_len'],
                    ),
                    'constraints' => array(
                        new Assert\NotBlank(),
                    ),
                ))
            ->add('other_deliv', 'entity', array(
                    'class' => 'Eccube\Entity\OtherDeliv',
                    'property' => 'name01',
                    'query_builder' => function (\Eccube\Repository\OtherDelivRepository $er) use ($Customer) {
                            return $er
                                ->createQueryBuilder('od')
                                ->where('od.Customer = :Customer')
                                ->orderBy("od.id", "ASC")
                                ->setParameter('Customer', $Customer);
                    },
                ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shipping_multi';
    }
}
