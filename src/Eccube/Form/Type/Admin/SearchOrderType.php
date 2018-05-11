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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Eccube\Common\EccubeConfig;
use Eccube\Form\Type\PriceType;
use Eccube\Form\Type\Master\OrderStatusType;
use Eccube\Form\Type\Master\PaymentType;
use Eccube\Form\Type\Master\SexType;

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
                'label' => 'searchorder.label.multi',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array('max' => $this->eccubeConfig['eccube_stext_len'])),
                ),
            ))
            ->add('status', OrderStatusType::class, array(
                'label' => 'searchorder.label.status',
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('name', TextType::class, array(
                'label' => 'searchorder.label.name',
                'required' => false,
            ))
            ->add($builder
                ->create('kana', TextType::class, array(
                    'label' => 'searchorder.label.kana',
                    'required' => false,
                    'constraints' => array(
                        new Assert\Regex(array(
                            'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
                            'message' => 'form.type.admin.notkanastyle',
                        )),
                    ),
                ))
                ->addEventSubscriber(new \Eccube\Form\EventListener\ConvertKanaListener('CV')
            ))
            ->add('company_name', TextType::class, array(
                'label' => 'searchorder.label.company_name',
                'required' => false,
            ))
            ->add('email', TextType::class, array(
                'label' => 'searchorder.label.email',
                'required' => false,
            ))
            ->add('order_id', IntegerType::class, array(
                'label' => 'searchorder.label.order_id',
                'required' => false,
            ))
            ->add('tel', TextType::class, array(
                'label' => 'common.label.phone_number',
                'required' => false,
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => "/^[\d-]+$/u",
                        'message' => 'form.type.admin.nottelstyle',
                    )),
                ),
            ))
            ->add('sex', SexType::class, array(
                'label' => 'searchorder.label.sex',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('payment', PaymentType::class, array(
                'label' => 'searchorder.label.payment_method',
                'required' => false,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('order_date_start', DateType::class, array(
                'label' => 'searchorder.label.order_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('order_date_end', DateType::class, array(
                'label' => 'searchorder.label.order_date_to',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('payment_date_start', DateType::class, array(
                'label' => 'searchorder.label.payment_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('payment_date_end', DateType::class, array(
                'label' => 'searchorder.label.payment_date_to',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('shipping_date_start', DateType::class, array(
                'label' => 'searchorder.label.shipping_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('shipping_date_end', DateType::class, array(
                'label' => 'searchorder.label.shipping_date_to',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('update_date_start', DateType::class, array(
                'label' => 'searchorder.label.updated_date_from',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('update_date_end', DateType::class, array(
                'label' => 'searchorder.label.updated_date_to',
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => array('year' => '----', 'month' => '--', 'day' => '--'),
            ))
            ->add('payment_total_start', PriceType::class, array(
                'label' => 'searchorder.label.purchased_amount_min',
                'required' => false,
            ))
            ->add('payment_total_end', PriceType::class, array(
                'label' => 'searchorder.label.purchased_amount_max',
                'required' => false,
            ))
            ->add('buy_product_name', TextType::class, array(
                'label' => 'searchorder.label.purchased_products',
                'required' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_search_order';
    }
}
