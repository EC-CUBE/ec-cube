<?php

namespace Eccube\Tests\Web\Admin\Order;

use Eccube\Entity\Customer;
use Eccube\Entity\Order;
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
     * @return array
     */
    public function createFormData(Customer $Customer, Product $Product = null)
    {
        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);

        $email = $faker->safeEmail;
        $delivery_date = $faker->dateTimeBetween('now', '+ 5 days');

        $OrderItems = array();
        if (is_object($Product)) {
            $ProductClasses = $Product->getProductClasses();
            $OrderItems[] = array(
                'Product' => $Product->getId(),
                'ProductClass' => $ProductClasses[0]->getId(),
                'price' => $ProductClasses[0]->getPrice02(),
                'quantity' => $faker->numberBetween(1, 999),
                'tax_rate' => 8, // XXX ハードコーディング
                'tax_rule' => 1,
                'product_name' => $Product->getName(),
                'product_code' => $ProductClasses[0]->getCode(),
            );
        }

        $order = array(
            '_token' => 'dummy',
            'Customer' => $Customer->getId(),
            'OrderStatus' => 1,
            'name' => array(
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ),
            'kana' => array(
                'kana01' => $faker->lastKanaName,
                'kana02' => $faker->firstKanaName,
            ),
            'company_name' => $faker->company,
            'zip' => array(
                'zip01' => $faker->postcode1(),
                'zip02' => $faker->postcode2(),
            ),
            'address' => array(
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ),
            'tel' => array(
                'tel01' => $tel[0],
                'tel02' => $tel[1],
                'tel03' => $tel[2],
            ),
            'fax' => array(
                'fax01' => $tel[0],
                'fax02' => $tel[1],
                'fax03' => $tel[2],
            ),
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
        );
        return $order;
    }

    /**
     * 受注再編集用フォーム作成.
     *
     * @param Order $Order
     * @return array
     */
    public function createFormDataForEdit(Order $Order)
    {
        //受注アイテム
        $orderItem = array();
        $OrderItemColl = $Order->getOrderItems();
        foreach ($OrderItemColl as $OrderItem) {
            $Product = $OrderItem->getProduct();
            $ProductClass = $OrderItem->getProductClass();
            $orderItem[] = array(
                'Product' => is_object($Product) ? $Product->getId() : null,
                'ProductClass' => is_object($ProductClass) ? $ProductClass->getId() : null,
                'price' => $OrderItem->getPrice(),
                'quantity' => $OrderItem->getQuantity(),
                'tax_rate' => $OrderItem->getTaxRate(),
                'tax_rule' => $OrderItem->getTaxRule(),
                'product_name' => is_object($Product) ? $Product->getName() : '送料', // XXX v3.1 より 送料等, Product の無い明細が追加される
                'product_code' => is_object($ProductClass) ? $ProductClass->getCode() : null,
            );
        }
        $Customer = $Order->getCustomer();
        $customer_id = null;
        if (is_object($Customer)) {
            $customer_id = $Customer->getId();
        }
        //受注フォーム
        $order = array(
            '_token' => 'dummy',
            'OrderStatus' => (string) $Order->getOrderStatus()->getId(),
            'Customer' => (string) $customer_id,
            'name' =>
            array(
                'name01' => $Order->getName01(),
                'name02' => $Order->getName02(),
            ),
            'kana' =>
            array(
                'kana01' => $Order->getKana01(),
                'kana02' => $Order->getKana02(),
            ),
            'zip' =>
            array(
                'zip01' => $Order->getZip01(),
                'zip02' => $Order->getZip02(),
            ),
            'address' =>
            array(
                'pref' => $Order->getPref()->getId(),
                'addr01' => $Order->getAddr01(),
                'addr02' => $Order->getAddr02(),
            ),
            'email' => $Order->getEmail(),
            'tel' =>
            array(
                'tel01' => $Order->getTel01(),
                'tel02' => $Order->getTel02(),
                'tel03' => $Order->getTel03(),
            ),
            'fax' =>
            array(
                'fax01' => $Order->getFax01(),
                'fax02' => $Order->getFax02(),
                'fax03' => $Order->getFax03(),
            ),
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
        );
        return $order;
    }
}
