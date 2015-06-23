<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Form\Type\Master;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
        $builder->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Eccube\Entity\Master\ProductListMax',
            'query_builder' => function (EntityRepository $er) {
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
