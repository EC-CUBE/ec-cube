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


namespace Eccube\Service;

use Doctrine\ORM\EntityManager;
use Eccube\Common\Constant;
use Eccube\Entity\CartItem;
use Eccube\Entity\Master\Disp;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\ProductClass;
use Eccube\Exception\CartException;
use Symfony\Component\HttpFoundation\Session\Session;

class CartService
{
    /** @var \Eccube\Application */
    public $app;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var \Eccube\Entity\Cart
     */
    private $cart;

    /**
     * @var \Eccube\Entity\BaseInfo
     */
    private $BaseInfo;

    /**
     * @var array
     */
    private $errors = array();

    private $ProductType = null;

    /**
     * @var array
     */
    private $messages = array();

    /**
     * @var array
     */
    private $error;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
        $this->session = $app['session'];
        $this->entityManager = $app['orm.em'];

        if ($this->session->has('cart')) {
            $this->cart = $this->session->get('cart');
        } else {
            $this->cart = new \Eccube\Entity\Cart();
        }

        $this->loadProductClassFromCart();

        $this->BaseInfo = $app['eccube.repository.base_info']->get();
    }

    /**
     * カートに保存されている商品の ProductClass エンティティを読み込み、カートへ設定します。
     */
    protected function loadProductClassFromCart()
    {
        /* @var $softDeleteFilter \Eccube\Doctrine\Filter\SoftDeleteFilter */
        // $softDeleteFilter = $this->entityManager->getFilters()->getFilter('soft_delete');
        // $excludes = $softDeleteFilter->getExcludes();
        // $softDeleteFilter->setExcludes(array(
        //     'Eccube\Entity\ProductClass',
        // ));

        // foreach ($this->cart->getCartItems() as $CartItem) {
        //     $this->loadProductClassFromCartItem($CartItem);
        // }

        // $softDeleteFilter->setExcludes($excludes);
    }

    /**
     * CartItem に対応する ProductClass を設定します。
     *
     * @param CartItem $CartItem
     */
    protected function loadProductClassFromCartItem(CartItem $CartItem)
    {
        $ProductClass = $this
            ->entityManager
            ->getRepository($CartItem->getClassName())
            ->find($CartItem->getClassId());

        $CartItem->setObject($ProductClass);

        if (is_null($this->ProductType) && $ProductClass->getDelFlg() == Constant::DISABLED) {
            $this->setCanAddProductType($ProductClass->getProductType());
        }
    }

    public function setCanAddProductType(\Eccube\Entity\Master\ProductType $ProductType)
    {
        if (is_null($this->ProductType)) {
            $this->ProductType = $ProductType;
        }

        return $this;
    }

    public function save()
    {
        return $this->session->set('cart', $this->cart);
    }

    public function unlock()
    {
        $this->cart
            ->setLock(false)
            ->setPreOrderId(null);
    }

    public function lock()
    {
        $this->cart
            ->setLock(true)
            ->setPreOrderId(null);
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->cart->getLock();
    }

    /**
     * @param  string $pre_order_id
     * @return \Eccube\Service\CartService
     */
    public function setPreOrderId($pre_order_id)
    {
        $this->cart->setPreOrderId($pre_order_id);

        return $this;
    }

    /**
     * @return string
     */
    public function getPreOrderId()
    {
        return $this->cart->getPreOrderId();
    }

    /**
     * @return \Eccube\Service\CartService
     */
    public function clear()
    {
        $this->cart
            ->setPreOrderId(null)
            ->setLock(false)
            ->clearCartItems();

        return $this;
    }

    public function getCanAddProductType()
    {
        return $this->ProductType;
    }

    /**
     *
     * @param  string $productClassId
     * @param  integer $quantity
     * @return \Eccube\Service\CartService
     */
    public function addProduct($productClassId, $quantity = 1)
    {
        $quantity += $this->getProductQuantity($productClassId);
        $this->setProductQuantity($productClassId, $quantity);

        return $this;
    }

    /**
     * @param  string $productClassId
     * @return integer
     */
    public function getProductQuantity($productClassId)
    {
        $CartItem = $this->cart->getCartItemByIdentifier('Eccube\Entity\ProductClass', (string)$productClassId);
        if ($CartItem) {
            return $CartItem->getQuantity();
        } else {
            return 0;
        }
    }

    /**
     * @param  \Eccube\Entity\ProductClass|integer $ProductClass
     * @param  integer $quantity
     * @return \Eccube\Service\CartService
     * @throws CartException
     */
    public function setProductQuantity($ProductClass, $quantity)
    {
        if (!$ProductClass instanceof ProductClass) {
            $ProductClass = $this->entityManager
                ->getRepository('Eccube\Entity\ProductClass')
                ->find($ProductClass);
            if (!$ProductClass) {
                throw new CartException('cart.product.delete');
            }
        }

        if (!$this->isProductDisplay($ProductClass)) {
            throw new CartException('cart.product.not.status');
        }

        $productName = $this->getProductName($ProductClass);

        // 商品種別に紐づく配送業者を取得
        $deliveries = $this->app['eccube.repository.delivery']->getDeliveries($ProductClass->getProductType());

        if (count($deliveries) == 0) {
            // 商品種別が存在しなければエラー
            $this->removeProduct($ProductClass->getId());
            $this->addError('cart.product.not.producttype', $productName);
            throw new CartException('cart.product.not.producttype');
        }

        $this->setCanAddProductType($ProductClass->getProductType());

        if ($this->BaseInfo->getOptionMultipleShipping() != Constant::ENABLED) {
            if (!$this->canAddProduct($ProductClass->getId())) {
                // 複数配送対応でなければ商品種別が異なればエラー
                throw new CartException('cart.product.type.kind');
            }
        } else {
            // 複数配送の場合、同一支払方法がなければエラー
            if (!$this->canAddProductPayment($ProductClass->getProductType())) {
                throw new CartException('cart.product.payment.kind');
            }
        }

        $tmp_subtotal = 0;
        $tmp_quantity = 0;
        foreach ($this->getCartObj()->getCartItems() as $cartitem) {
            $pc = $cartitem->getObject();
            if ($pc->getId() != $ProductClass->getId()) {
                // 追加された商品以外のtotal priceをセット
                $tmp_subtotal += $cartitem->getTotalPrice();
            }
        }
        for ($i = 0; $i < $quantity; $i++) {
            $tmp_subtotal += $ProductClass->getPrice02IncTax();
            if ($tmp_subtotal > $this->app['config']['max_total_fee']) {
                $this->setError('cart.over.price_limit');
                break;
            }
            $tmp_quantity++;
        }
        if ($tmp_quantity == 0) {
            // 数量が0の場合、エラー
            throw new CartException('cart.over.price_limit');
        }

        // 制限数チェック(在庫不足の場合は、処理の中でカート内商品を削除している)
        $quantity = $this->setProductLimit($ProductClass, $productName, $tmp_quantity);

		// 新しい数量でカート内商品を登録する
        if (0 < $quantity) {
            $CartItem = new CartItem();
            $CartItem
                ->setClassName('Eccube\Entity\ProductClass')
                ->setClassId((string)$ProductClass->getId())
                ->setPrice($ProductClass->getPrice02IncTax())
                ->setQuantity($quantity);

            $this->cart->setCartItem($CartItem);
        }

        return $this;
    }

    /**
     * @param  string $productClassId
     * @return boolean
     */
    public function canAddProduct($productClassId)
    {
        $ProductClass = $this
            ->entityManager
            ->getRepository('\Eccube\Entity\ProductClass')
            ->find($productClassId);

        if (!$ProductClass) {
            return false;
        }

        $ProductType = $ProductClass->getProductType();

        return $this->ProductType == $ProductType;
    }

    /**
     * @param \Eccube\Entity\Master\ProductType $ProductType
     * @return bool
     */
    public function canAddProductPayment(\Eccube\Entity\Master\ProductType $ProductType)
    {
        $deliveries = $this
            ->entityManager
            ->getRepository('\Eccube\Entity\Delivery')
            ->findBy(array('ProductType' => $ProductType));

        // 支払方法を取得
        $payments = $this->entityManager->getRepository('Eccube\Entity\Payment')->findAllowedPayments($deliveries);

        if ($this->getCart()->getTotalPrice() < 1) {
            // カートになければ支払方法を全て設定
            $this->getCart()->setPayments($payments);

            return true;
        }

        // カートに存在している支払方法と追加された商品の支払方法チェック
        $arr = array();
        foreach ($payments as $payment) {
            foreach ($this->getCart()->getPayments() as $p) {
                if ($payment['id'] == $p['id']) {
                    $arr[] = $payment;
                    break;
                }
            }
        }

        if (count($arr) > 0) {
            $this->getCart()->setPayments($arr);

            return true;
        }

        // 支払条件に一致しない
        return false;

    }

    /**
     * カートブロックに表示するカートを取得します。
     * ブロックに表示するカートはチェックを行わず、セットされているカートを返します。
     *
     * @return \Eccube\Entity\Cart
     */
    public function getCartObj()
    {

        foreach ($this->cart->getCartItems() as $CartItem) {

            /** @var \Eccube\Entity\ProductClass $ProductClass */
            $ProductClass = $CartItem->getObject();
            if (!$ProductClass) {
                $this->loadProductClassFromCartItem($CartItem);

                $ProductClass = $CartItem->getObject();
            }

            if ($ProductClass->getDelFlg()) {
                // 商品情報が削除されていたらエラー
                $this->setError('cart.product.delete');
                // カートから削除
                $this->removeProduct($ProductClass->getId());
            }
        }

        return $this->cart;

    }

    /**
     * カートを取得します。
     *
     * @return \Eccube\Entity\Cart
     */
    public function getCart()
    {
        foreach ($this->cart->getCartItems() as $CartItem) {

            /** @var \Eccube\Entity\ProductClass $ProductClass */
            $ProductClass = $CartItem->getObject();
            if (!$ProductClass) {
                $this->loadProductClassFromCartItem($CartItem);

                $ProductClass = $CartItem->getObject();
            }

            if ($ProductClass->getDelFlg() == Constant::DISABLED) {
                // 商品情報が有効

                if (!$this->isProductDisplay($ProductClass)) {
                    $this->setError('cart.product.not.status');
                } else {

                    $productName = $this->getProductName($ProductClass);

                    // 制限数チェック(在庫不足の場合は、処理の中でカート内商品を削除している)
                    $quantity = $this->setProductLimit($ProductClass, $productName, $CartItem->getQuantity());

                    /// 個数が異なれば、新しい数量でカート内商品を更新する
                    if ((0 < $quantity) && ($CartItem->getQuantity() != $quantity)) {
                        // 個数が異なれば更新
                        $CartItem->setQuantity($quantity);
                        $this->cart->setCartItem($CartItem);
                    }
                }

            } else {
                // 商品情報が削除されていたらエラー
                $this->setError('cart.product.delete');
                // カートから削除
                $this->removeProduct($ProductClass->getId());
            }
        }

        return $this->cart;
    }

    /**
     * @param  string $productClassId
     * @return \Eccube\Service\CartService
     */
    public function removeProduct($productClassId)
    {
        $this->cart->removeCartItemByIdentifier('Eccube\Entity\ProductClass', (string)$productClassId);

        // 支払方法の再設定
        if ($this->BaseInfo->getOptionMultipleShipping() == Constant::ENABLED) {

            // 複数配送対応
            $productTypes = array();
            foreach ($this->getCart()->getCartItems() as $item) {
                /* @var $ProductClass \Eccube\Entity\ProductClass */
                $ProductClass = $item->getObject();
                $productTypes[] = $ProductClass->getProductType();
            }

            // 配送業者を取得
            $deliveries = $this->entityManager->getRepository('Eccube\Entity\Delivery')->getDeliveries($productTypes);

            // 支払方法を取得
            $payments = $this->entityManager->getRepository('Eccube\Entity\Payment')->findAllowedPayments($deliveries);

            $this->getCart()->setPayments($payments);
        }

        return $this;
    }

    /**
     * @param  string $error
     * @param  string $productName
     * @return \Eccube\Service\CartService
     */
    public function addError($error = null, $productName = null)
    {
        $this->errors[] = $error;
        $this->session->getFlashBag()->add('eccube.front.request.error', $error);
        if (!is_null($productName)) {
            $this->session->getFlashBag()->add('eccube.front.request.product', $productName);
        }

        return $this;
    }

    /**
     * @param  string $productClassId
     * @return \Eccube\Service\CartService
     */
    public function upProductQuantity($productClassId)
    {
        $quantity = $this->getProductQuantity($productClassId) + 1;
        $this->setProductQuantity($productClassId, $quantity);

        return $this;
    }

    /**
     * @param  string $productClassId
     * @return \Eccube\Service\CartService
     */
    public function downProductQuantity($productClassId)
    {
        $quantity = $this->getProductQuantity($productClassId) - 1;
        if ($quantity > 0) {
            $this->setProductQuantity($productClassId, $quantity);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getProductTypes()
    {

        $productTypes = array();
        foreach ($this->getCart()->getCartItems() as $item) {
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ProductClass = $item->getObject();
            $productTypes[] = $ProductClass->getProductType();
        }

        return array_unique($productTypes);

    }

    /**
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param  string $message
     * @return \Eccube\Service\CartService
     */
    public function setMessage($message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param  string $error
     * @return \Eccube\Service\CartService
     */
    public function setError($error = null)
    {
        $this->error = $error;
        $this->session->getFlashBag()->set('eccube.front.request.error', $error);

        return $this;
    }

    /**
     * 商品名を取得
     *
     * @param ProductClass $ProductClass
     * @return string
     */
    private function getProductName(ProductClass $ProductClass)
    {

        $productName = $ProductClass->getProduct()->getName();

        if ($ProductClass->hasClassCategory1()) {
            $productName .= " - ".$ProductClass->getClassCategory1()->getName();
        }

        if ($ProductClass->hasClassCategory2()) {
            $productName .= " - ".$ProductClass->getClassCategory2()->getName();
        }

        return $productName;
    }


    /**
     * 非公開商品の場合、カートから削除
     *
     * @param ProductClass $ProductClass
     * @return bool
     */
    private function isProductDisplay(ProductClass $ProductClass)
    {

        if ($ProductClass->getProduct()->getStatus()->getId() !== Disp::DISPLAY_SHOW) {
            // 非公開の商品はカートから削除
            $this->removeProduct($ProductClass->getId());

            return false;
        }

        return true;
    }


    /**
     * 在庫数と販売制限数のチェック
     * 在庫数または販売制限数以上の個数が設定されていれば、それぞれの個数にセットし、
     * 在庫数と販売制限数ともに個数が超えていれば、少ない方を適用させてメッセージを表示する
     *
     * @param ProductClass $ProductClass
     * @param $productName
     * @param $quantity
     * @return int チェック後に更新した個数
     */
    private function setProductLimit(ProductClass $ProductClass, $productName, $quantity)
    {
        return;

        /**
         * 実際の在庫は ProductClass::ProductStock だが、購入時にロックがかかるため、
         * ここでは ProductClass::stock で在庫のチェックをする
         */

        // 在庫数(在庫無制限の場合、null)
        $stock = $ProductClass->getStock();
        // 在庫無制限(在庫無制限の場合、1)
        $stockUnlimited = $ProductClass->getStockUnlimited();

        // 販売制限数(設定されていなければnull)
        $saleLimit = $ProductClass->getSaleLimit();

        if ($stockUnlimited) {
            // 在庫無制限

            if ($saleLimit && $saleLimit < $quantity) {
                // 販売制限数を超えていれば販売制限数をセット
                $this->addError('cart.over.sale_limit', $productName);

                return $saleLimit;
            }
        } else {
            // 在庫制限あり

            if ($stock < 1) {
                // 在庫がなければカートから削除
                $this->addError('cart.zero.stock', $productName);
                $this->removeProduct($ProductClass->getId());

                return 0;
            } else {
                // 在庫数チェックと販売制限数チェックどちらを適用するか設定
                $message = 'cart.over.stock';
                if ($saleLimit) {
                    if ($stock > $saleLimit) {
                        // 販売制限数チェック
                        $limit = $saleLimit;
                        $message = 'cart.over.sale_limit';
                    } else {
                        // 在庫数チェック
                        $limit = $stock;
                    }
                } else {
                    // 在庫数チェック
                    $limit = $stock;
                }

                if ($limit < $quantity) {
                    // 在庫数、販売制限数を超えていれば購入可能数までをセット
                    $this->addError($message, $productName);

                    return $limit;
                }
            }
        }

        return $quantity;
    }

}
