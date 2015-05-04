<?php

namespace Eccube\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DelivTimeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('deliv_id', 'hidden')
            ->add('time_id', 'hidden')
            ->add('deliv_time', 'text')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\DelivTime',
            'query_builder' => function (EntityRepository $er) {
                return $er
                    ->createQueryBuilder('dt')
                    ->orderBy('dt.time_id', 'ASC');
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'deliv_time';
    }

}
