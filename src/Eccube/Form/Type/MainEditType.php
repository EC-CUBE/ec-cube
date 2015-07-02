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

class MainEditType extends AbstractType
{
    public $app;

    public function __construct(\Silex\Application $app)
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
                'label' => '名称',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('file_name', 'text', array(
                'label' => 'URL',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => "/^[0-9a-zA-Z]+$/",
                    )),
                )
            ))
            ->add('tpl_data', 'textarea', array(
                'label' => false,
                'mapped' => false,
                'required' => true,
                'constraints' => array()
            ))
            ->add('author', 'text', array(
                'label' => 'author',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('description', 'text', array(
                'label' => 'description',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('keyword', 'text', array(
                'label' => 'keyword',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('meta_robots', 'text', array(
                'label' => 'robots',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    ))
                )
            ))
            ->add('DeviceType', 'entity', array(
                'class' => 'Eccube\Entity\Master\DeviceType',
                'property' => 'id',
            ))
            ->add('id', 'hidden')
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) use ($app) {
                $form = $event->getForm();
                $file_name = $form['file_name']->getData();
                $DeviceType = $form['DeviceType']->getData();
                $page_id = $form['id']->getData();

                $qb = $app['orm.em']->createQueryBuilder();
                $qb->select('p')
                    ->from('Eccube\\Entity\\PageLayout', 'p')
                    ->where('p.file_name = :file_name')
                    ->setParameter('file_name', $file_name)
                    ->andWhere('p.DeviceType = :DeviceType')
                    ->setParameter('DeviceType', $DeviceType)
                ;
                if (is_null($page_id)) {
                    $qb
                        ->andWhere('p.id IS NOT NULL');
                } else {
                    $qb
                        ->andWhere('p.id <> :page_id')
                        ->setParameter('page_id', $page_id);
                }

                $PageLayout = $qb
                    ->getQuery()
                    ->getResult();
                if (count($PageLayout) > 0) {
                    $form['file_name']->addError(new FormError('※ 同じURLのデータが存在しています。別のURLを入力してください。'));
                }
            })
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\PageLayout',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'main_edit';
    }
}
