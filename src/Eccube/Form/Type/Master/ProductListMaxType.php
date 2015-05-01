<?php

namespace Eccube\Form\Type\Master;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityRepository;

class ProductListMaxType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $options = $event->getForm()->getConfig()->getOptions();
            if (!$event->getData()) {
                $data = current(array_keys($options['choice_list']->getValues()));
                $event->setData($data);
            }
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $options = $event->getForm()->getConfig()->getOptions();
            $values = $options['choice_list']->getValues();
            if (!in_array($event->getData(), $values)) {
                $data = current($values);
                $event->setData($data);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Eccube\Entity\Master\ProductListMax',
            'query_builder' => function(EntityRepository $er) {
                return $er
                    ->createQueryBuilder('pn')
                    ->orderBy('pn.rank', 'ASC');
            },
            'property' => 'name',
            'label' => false,
            'multiple'=> false,
            'expanded' => false,
            'required' => false,
            'empty_value' => false,
            'empty_data' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'product_list_max';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }
}
