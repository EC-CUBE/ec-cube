<?php

namespace Eccube\Service;

use Eccube\Entity\Cart;

class CartCompareService
{
    /** @var Cart  */
    protected $Cart;

    /** @var \Eccube\Service\CartComparator\CompareContext */
    protected $context;

    /**
     * @param $Cart
     */
    public function __construct($Cart)
    {
        $this->Cart = $Cart;
    }

    /**
     * @param \Eccube\Service\CartComparator\CompareContext $context
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return \Eccube\Service\CartComparator\CompareContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * CompareContext::compare()のエイリアス
     *
     * @param \Eccube\Entity\CartItem $CartItem1
     * @param \Eccube\Entity\CartItem $CartItem2
     * @return bool
     */
    public function compare($CartItem1, $CartItem2)
    {
        return $this->context->compare($CartItem1, $CartItem2);
    }

    /**
     * カート内商品と比較し、既に存在する商品を取得する
     *
     * @param \Eccube\Entity\CartItem $CartItem
     * @return \Eccube\Entity\CartItem|null 存在しない場合はnull
     */
    public function getExistsCartItem($CartItem)
    {
        $result = null;

        foreach ($this->Cart->getCartItems() as $CompareCartItem) {
            if ($this->context->compare($CartItem, $CompareCartItem)) {
                $result = $CompareCartItem;
                break;
            }
        }

        return $result;
    }
}
