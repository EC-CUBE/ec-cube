<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
use Symfony\Component\Validator\Constraints as Assert;

class ShippingMultipleType extends AbstractType
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
            ->addEventListener(FormEvents::POST_SET_DATA, function ($event) use ($app) {
                /** @var \Eccube\Entity\ShipmentItem $data */
                $data = $event->getData();
                /** @var \Symfony\Component\Form\Form $form */
                $form = $event->getForm();

                if (is_null($data)) {
                    return;
                }

                $shippings = $app['eccube.repository.shipping']->findShippingsProduct($data->getOrder(), $data->getProductClass());

                // Add product class for each shipping on view
                foreach ($shippings as $key => $shipping) {
                    $shippingTmp = clone $shipping->setProductClassOfTemp($data->getProductClass());
                    $shippings[$key] = $shippingTmp;
                }
                $form
                    ->add('shipping', 'collection', array(
                        'type' => 'shipping_multiple_item',
                        'data' => $shippings,
                        'allow_add' => true,
                        'allow_delete' => true,
                    ));
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shipping_multiple';
    }
}
