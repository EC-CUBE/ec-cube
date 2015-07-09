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


namespace Eccube\Form\Type\Admin;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;
use Eccube\Common\Constant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CsvType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $CsvType = $options['CsvType'];

        $builder
            ->add('csv_type', 'csv_type', array(
                'label' => 'CSV出力項目',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
                'mapped' => false,
            ))
            ->add('csv_not_output', 'choice', array(
                'class' => 'Eccube\Entity\Csv',
                'property' => 'disp_name',
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) use ($CsvType) {
                    return $er
                        ->createQueryBuilder('c')
                        ->where('c.CsvType = :CsvType')
                        ->andWhere('c.enable_flg = ' . Constant::DISABLED)
                        ->setParameter('CsvType', $CsvType)
                        ->orderBy('c.rank', 'ASC');
                },
            ))
            /*
            ->add('csv_not_output', 'entity', array(
                'class' => 'Eccube\Entity\Csv',
                'property' => 'disp_name',
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) use ($CsvType) {
                    return $er
                        ->createQueryBuilder('c')
                        ->where('c.CsvType = :CsvType')
                        ->andWhere('c.enable_flg = ' . Constant::DISABLED)
                        ->setParameter('CsvType', $CsvType)
                        ->orderBy('c.rank', 'ASC');
                },
            ))
            ->add('csv_output', 'entity', array(
                'class' => 'Eccube\Entity\Csv',
                'property' => 'disp_name',
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) use ($CsvType) {
                    return $er
                        ->createQueryBuilder('c')
                        ->where('c.CsvType = :CsvType')
                        ->andWhere('c.enable_flg = ' . Constant::ENABLED)
                        ->setParameter('CsvType', $CsvType)
                        ->orderBy('c.rank', 'ASC');
                },
            ))
            */
            /*
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($CsvType) {
                $form = $event->getForm();
                $data = $event->getData();

                $form->add('csv_not_output_list', 'entity', array(
                    'class' => 'Eccube\Entity\Csv',
                    'property' => 'disp_name',
                    'multiple' => true,
                    'query_builder' => function (EntityRepository $er) use ($CsvType) {
                        return $er
                            ->createQueryBuilder('c')
                            ->where('c.CsvType = :CsvType')
                            ->andWhere('c.enable_flg = ' . Constant::DISABLED)
                            ->setParameter('CsvType', $CsvType)
                            ->orderBy('c.rank', 'ASC');
                    },
                ));

            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                unset($data['csv_not_output']);
                $event->getForm()->remove('csv_not_output');
                $event->setData($data);
            })
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                if (!$form->has('csv_not_output')) {
                    $existing_customer = $form->get('csv_not_output_list')->getData();
                    // $data['csv_not_output'] = $existing_customer;
                    // $data['csv_not_output'] = array('hoge');

                    Debug::dump($data['csv_not_output']);
                    $form->get('csv_not_output')->setData($data['csv_not_output']);
   //                 $data->setCsvNotOutput($existing_customer);
                }
            })
            */
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'CsvType' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        //       array_unshift($view->children['csv_not_output_list']->vars['choices'], new ChoiceView('test', 'new', 'Nouveau client'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'csv';
    }
}
