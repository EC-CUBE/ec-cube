<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Entity\Customer;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Product;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * EditController 用 WebTest の抽象クラス.
 *
 * Admin\Order\EditController の WebTest をする場合に汎用的に使用する.
 *
 * @author Kentaro Ohkouchi
 */
abstract class AbstractEditControllerTestCase extends AbstractAdminWebTestCase
{
    /**
     * 受注編集用フォーム作成.
     *
     * @param Customer $Customer
     * @param Product $Product
     *
     * @return array
     */
    public function createFormData(Customer $Customer, Product $Product = null)
    {
        $faker = $this->getFaker();
        $email = $faker->safeEmail;
        $delivery_date = $faker->dateTimeBetween('now', '+ 5 days');

        $OrderItems = [];
        if (is_object($Product)) {
            $ProductClasses = $Product->getProductClasses();
            $OrderItems[] = [
                'Product' => $Product->getId(),
                'ProductClass' => $ProductClasses[0]->getId(),
                'price' => $ProductClasses[0]->getPrice02(),
                'quantity' => $faker->numberBetween(1, 999),
                'tax_rate' => 8, // XXX ハードコーディング
                'tax_rule' => 1,
                'tax_type' => TaxType::TAXATION,
                'product_name' => $Product->getName(),
                'product_code' => $ProductClasses[0]->getCode(),
                'tax_display_type' => TaxDisplayType::EXCLUDED,
                'order_item_type' => OrderItemType::PRODUCT,
            ];
        }

        $shipping = [
            'name' => [
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ],
            'kana' => [
                'kana01' => $faker->lastKanaName,
                'kana02' => $faker->firstKanaName,
            ],
            'postal_code' => $faker->postcode,
            'address' => [
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ],
            'phone_number' => $faker->phoneNumber,
            'Delivery' => 1,
        ];

        $order = [
            '_token' => 'dummy',
            'Customer' => $Customer->getId(),
            'OrderStatus' => 1,
            'name' => [
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ],
            'kana' => [
                'kana01' => $faker->lastKanaName,
                'kana02' => $faker->firstKanaName,
            ],
            'company_name' => $faker->company,
            'postal_code' => $faker->postcode,
            'address' => [
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ],
            'phone_number' => $faker->phoneNumber,
            'email' => $email,
            'message' => $faker->realText,
            'Payment' => 1,     // XXX ハードコーディング
            'discount' => 0,
            'delivery_fee_total' => 0,
            'charge' => 0,
            'note' => $faker->realText,
            'OrderItems' => $OrderItems,
            'add_point' => 0,
            'use_point' => 0,
            'Shipping' => $shipping,
        ];

        return $order;
    }

    /**
     * 受注再編集用フォーム作成.
     *
     * @param Order $Order
     *
     * @return array
     */
    public function createFormDataForEdit(Order $Order)
    {
        //受注アイテム
        $orderItem = [];
        $OrderItemColl = $Order->getOrderItems();
        /** @var OrderItem $OrderItem */
        foreach ($OrderItemColl as $OrderItem) {
            /** @var Product $Product */
            $Product = $OrderItem->getProduct();
            $ProductClass = $OrderItem->getProductClass();
            $orderItem[] = [
                'Product' => is_object($Product) ? $Product->getId() : null,
                'ProductClass' => is_object($ProductClass) ? $ProductClass->getId() : null,
                'price' => $OrderItem->getPrice(),
                'quantity' => $OrderItem->getQuantity(),
                'tax_rate' => $OrderItem->getTaxRate(),
                'tax_rule' => $OrderItem->getTaxRule(),
                'tax_type' => TaxType::TAXATION,
                'product_name' => is_object($Product) ? $Product->getName() : '送料', // XXX v3.1 より 送料等, Product の無い明細が追加される
                'product_code' => is_object($ProductClass) ? $ProductClass->getCode() : null,
                'order_item_type' => $OrderItem->getOrderItemTypeId(),
                'tax_display_type' => is_object($OrderItem->getTaxDisplayType()) ? $OrderItem->getTaxDisplayType()->getId() : null,
            ];
        }
        $Customer = $Order->getCustomer();
        $customer_id = null;
        if (is_object($Customer)) {
            $customer_id = $Customer->getId();
        }

        $Shipping = $Order->getShippings()[0];

        $shipping = [
            'name' => [
                'name01' => $Shipping->getName01(),
                'name02' => $Shipping->getName02(),
            ],
            'kana' => [
                'kana01' => $Shipping->getKana01(),
                'kana02' => $Shipping->getKana02(),
            ],
            'postal_code' => $Shipping->getPostalCode(),
            'address' => [
                'pref' => $Shipping->getPref()->getId(),
                'addr01' => $Shipping->getAddr01(),
                'addr02' => $Shipping->getAddr02(),
            ],
            'phone_number' => $Shipping->getPhoneNumber(),
            'Delivery' => 1,
        ];

        //受注フォーム
        $order = [
            '_token' => 'dummy',
            'OrderStatus' => (string) $Order->getOrderStatus()->getId(),
            'Customer' => (string) $customer_id,
            'name' => [
                'name01' => $Order->getName01(),
                'name02' => $Order->getName02(),
            ],
            'kana' => [
                'kana01' => $Order->getKana01(),
                'kana02' => $Order->getKana02(),
            ],
            'postal_code' => $Order->getPostalCode(),
            'address' => [
                'pref' => $Order->getPref()->getId(),
                'addr01' => $Order->getAddr01(),
                'addr02' => $Order->getAddr02(),
            ],
            'email' => $Order->getEmail(),
            'phone_number' => $Order->getPhoneNumber(),
            'company_name' => $Order->getCompanyName(),
            'message' => $Order->getMessage(),
            'OrderItems' => $orderItem,
            'discount' => $Order->getDiscount(),
            'delivery_fee_total' => $Order->getDeliveryFeeTotal(),
            'charge' => $Order->getCharge(),
            'Payment' => $Order->getPayment()->getId(),
            'note' => $Order->getNote(),
            'add_point' => 0,
            'use_point' => 0,
            'Shipping' => $shipping,
        ];

        return $order;
    }
}
