<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;

class ProductDetailCartItem extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return CartItemType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'product_detail_cart_item';
    }
}
