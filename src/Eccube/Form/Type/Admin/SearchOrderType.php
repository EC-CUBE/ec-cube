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

use Eccube\Common\EccubeConfig;
use Eccube\Form\Type\Master\OrderStatusType;
use Eccube\Form\Type\Master\PaymentType;
use Eccube\Form\Type\Master\SexType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SearchOrderType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // 受注ID・注文者名・注文者（フリガナ）・注文者会社名
            ->add('multi', TextType::class, array(
                'label' => '受注ID・注文者名・注文者（フリガナ）・注文者会社名',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array('max' => $this->eccubeConfig['stext_len'])),
                ),
            ))
            ->add('status', OrderStatusType::class, array(
                'label' => '対応状況',
            ))
            ->add('multi_status', OrderStatusType::class, array(
                'label' => '対応状況',
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('name', TextType::class, array(
                'required' => false,
            ))
            ->add('email', TextType::class, array(
                'required' => false,
            ))
            ->add('tel', TextType::class, array(
                'required' => false,
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => "/^[\d-]+$/u",
                        'message' => 'form.type.admin.nottelstyle',
                    )),
                ),
            ))
            ->add('sex', SexType::class, array(
                'label' => '性別',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('payment', PaymentType::class, array(
                'label' => '支払方法',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('order_date_start', DateType::class, array(
                'label' => '受注日(FROM)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('order_date_end', DateType::class, array(
                'label' => '受注日(TO)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('payment_date_start', DateType::class, array(
                'label' => '入金日(FROM)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('payment_date_end', DateType::class, array(
                'label' => '入金日(TO)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('shipping_date_start', DateType::class, array(
                'label' => '発送日(FROM)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('shipping_date_end', DateType::class, array(
                'label' => '発送日(TO)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('update_date_start', DateType::class, array(
                'label' => '更新日(FROM)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('update_date_end', DateType::class, array(
                'label' => '更新日(TO)',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('payment_total_start', IntegerType::class, array(
                'label' => '購入金額(下限)',
                'required' => false,
            ))
            ->add('payment_total_end', IntegerType::class, array(
                'label' => '購入金額(上限)',
                'required' => false,
            ))
            ->add('buy_product_name', TextType::class, array(
                'label' => '購入商品名',
                'required' => false,
            ));

        $builder->add(
            $builder
                ->create('kana', TextType::class, array(
                    'required' => false,
                    'constraints' => array(
                        new Assert\Regex(array(
                            'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
                            'message' => 'form.type.admin.notkanastyle',
                        )),
                    ),
                ))
                ->addEventSubscriber(new \Eccube\Form\EventListener\ConvertKanaListener('CV'))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_search_order';
    }
}
