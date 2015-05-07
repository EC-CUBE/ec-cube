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


namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class BlockType extends AbstractType
{
    public $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $builder
            ->add('name', 'text', array(
                'label' => 'ブロック名',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('filename', 'text', array(
                'label' => 'ファイル名',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $app['config']['lltext_len'],
                    )),
                )
            ))
            ->add('bloc_html', 'textarea', array(
                'label' => 'ブロックデータ',
                'mapped' => false,
                'required' => true,
                'constraints' => array()
            ))
            ->add('device_type_id', 'hidden')
            ->add('bloc_id', 'hidden')
            ->add('save', 'submit', array('label' => 'この内容で登録する'))
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $filename = $form['filename']->getData();
                $device_type_id = $form['device_type_id']->getData();
                $block_id = $form['bloc_id']->getData();
                $qb = $this->app['orm.em']->createQueryBuilder();
                $qb->select('b')
                    ->from('Eccube\\Entity\\Bloc', 'b')
                    ->where('b.filename = :filename')
                    ->setParameter('filename', $filename)
                    ->andWhere('b.device_type_id = :device_type_id')
                    ->setParameter('device_type_id', $device_type_id)
                    ->andWhere('b.bloc_id <> :block_id')
                    ->setParameter('block_id', $block_id)
                ;

                $Block = $qb
                    ->getQuery()
                    ->getResult();
                if (count($Block) > 0) {
                    $form['filename']->addError(new FormError('※ 同じファイル名のデータが存在しています。別のファイル名を入力してください。'));
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Bloc',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'block';
    }
}
