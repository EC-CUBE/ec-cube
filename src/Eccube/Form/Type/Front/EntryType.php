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


namespace Eccube\Form\Type\Front;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class EntryType extends AbstractType
{
    protected $app;

    public function __construct($app)
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
            ->add('name', 'name', array(
                'required' => true,
            ))
            ->add('kana', 'kana', array(
                'required' => true,
            ))
            ->add('company_name', 'text', array(
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $app['config']['stext_len'],
                    )),
                ),
            ))
            ->add('zip', 'zip')
            ->add('address', 'address')
            ->add('tel', 'tel', array(
                'required' => true,
            ))
            ->add('fax', 'tel', array(
                'required' => false,
            ))
            ->add('email', 'repeated_email')
            ->add('password', 'repeated_password')
            ->add('birth', 'birthday', array(
                'required' => false,
                'input' => 'datetime',
                'years' => range(date('Y'), date('Y') - $app['config']['birth_max']),
                'widget' => 'choice',
                'format' => 'yyyy/MM/dd',
                'empty_value' => array('year' => '----', 'month' => '--', 'day' => '--'),
                'constraints' => array(
                    new Assert\LessThanOrEqual(array(
                        'value' => date('Y-m-d'),
                        'message' => 'form.type.select.selectisfuturedate',
                    )),
                ),
            ))
            ->add('sex', 'sex', array(
                'required' => false,
            ))
            ->add('job', 'job', array(
                'required' => false,
            ))
            ->add('save', 'submit', array('label' => 'この内容で登録する'))
            ->addEventListener(FormEvents::POST_SUBMIT, function($event) use ($app) {
                $form = $event->getForm();
                $email = $form['email']->getData();

                if ($app->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                    $previous = $app['orm.em']
                        ->getUnitOfWork()
                        ->getOriginalEntityData($app->user());

                    if (isset($previous['email']) && $previous['email']==$email) {
                        return;
                    }
                }

                $CustomerStatus = $app['orm.em']->createQueryBuilder()
                    ->select('cs')
                    ->from('Eccube\Entity\Master\CustomerStatus', 'cs')
                    ->where('cs.id = :id')
                    ->setParameter('id', \Eccube\Entity\Master\CustomerStatus::ACTIVE)
                    ->getQuery()
                    ->getSingleResult();

                $qb = $app['orm.em']->createQueryBuilder()
                    ->select('c')
                    ->from('Eccube\Entity\Customer', 'c')
                    ->where('c.email = :email')
                    ->setParameter('email', $email)
                    ->andWhere('c.Status = :Status')
                    ->setParameter('Status', $CustomerStatus);

                $Customer = $qb->getQuery()->getResult();
                if (0 < count($Customer)) {
                    $form['email']['first']->addError(new FormError('既に利用されているメールアドレスです'));
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
            'data_class' => 'Eccube\Entity\Customer',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        // todo entry,mypageで共有されているので名前を変更する
        return 'entry';
    }
}
