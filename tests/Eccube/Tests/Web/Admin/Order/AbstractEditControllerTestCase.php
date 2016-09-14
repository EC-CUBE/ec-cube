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

        $OrderDetails = array();
        if (is_object($Product)) {
            $ProductClasses = $Product->getProductClasses();
            $OrderDetails[] = array(
                'Product' => $Product->getId(),
                'ProductClass' => $ProductClasses[0]->getId(),
                'price' => $ProductClasses[0]->getPrice02(),
                'quantity' => $faker->randomNumber(2),
                'tax_rate' => 8, // XXX ハードコーディング
                'tax_rule' => 1,
                'product_name' => $Product->getName(),
                'product_code' => $ProductClasses[0]->getCode(),
            );
        }

        $Shippings = array(
            array(
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
                    'pref' => $faker->numberBetween(1, 47),
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
                'Delivery' => 1, // XXX ハードコーディング
                'DeliveryTime' => 1, // XXX ハードコーディング
                'shipping_delivery_date' => array(
                    'year' => $delivery_date->format('Y'),
                    'month' => $delivery_date->format('n'),
                    'day' => $delivery_date->format('j')
                )
            )
        );

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
            'message' => $faker->text,
            'Payment' => 1,     // XXX ハードコーディング
            'discount' => 0,
            'delivery_fee_total' => 0,
            'charge' => 0,
            'note' => $faker->text,
            'OrderDetails' => $OrderDetails,
            'Shippings' => $Shippings
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
        $orderDetail = array();
        $OrderDetailColl = $Order->getOrderDetails();
        foreach ($OrderDetailColl as $OrderDetail) {
            $orderDetail[] = array(
                'Product' => $OrderDetail->getProduct()->getId(),
                'ProductClass' => $OrderDetail->getProductClass()->getId(),
                'price' => $OrderDetail->getPrice(),
                'quantity' => $OrderDetail->getQuantity(),
                'tax_rate' => $OrderDetail->getTaxRate(),
                'tax_rule' => $OrderDetail->getTaxRule(),
                'product_name' => $OrderDetail->getProduct()->getName(),
                'product_code' => $OrderDetail->getProductClass()->getCode(),
            );
        }
        //受注お届け
        $shippings = array();
        $ShippingsColl = $Order->getShippings();
        foreach ($ShippingsColl as $Shippings) {
            $deliveryTime = '';
            if (is_object($Shippings->getDeliveryTime())) {
                $deliveryTime = $Shippings->getDeliveryTime()->getId();
            }
            $shippingDeliveryDate = array(
                'year' => null,
                'month' => null,
                'day' => null
            );

            if ($Shippings->getShippingDeliveryDate() instanceof \DateTime) {
                $shippingDeliveryDate['year'] = $Shippings->getShippingDeliveryDate()->format('Y');
                $shippingDeliveryDate['month'] = $Shippings->getShippingDeliveryDate()->format('n');
                $shippingDeliveryDate['day'] = $Shippings->getShippingDeliveryDate()->format('d');
            }
            $shipmentItems = array();
            /** @var \Eccube\Entity\ShipmentItem $ShipmentItem */
            foreach ($Shippings->getShipmentItems() as $ShipmentItem) {
                $shipmentItems[] = array(
                    'Product' => $ShipmentItem->getProduct()->getId(),
                    'ProductClass' => $ShipmentItem->getProductClass()->getId(),
                    'price' => $ShipmentItem->getPrice(),
                    'quantity' => $ShipmentItem->getQuantity(),
                    'product_name' => $ShipmentItem->getProduct()->getName(),
                    'product_code' => $ShipmentItem->getProductClass()->getCode(),
                );
            }

            $shippings[] = array(
                'name' =>
                array(
                    'name01' => $Shippings->getName01(),
                    'name02' => $Shippings->getName02(),
                ),
                'kana' =>
                array(
                    'kana01' => $Shippings->getKana01(),
                    'kana02' => $Shippings->getKana02(),
                ),
                'company_name' => $Shippings->getCompanyName(),
                'zip' =>
                array(
                    'zip01' => $Shippings->getZip01(),
                    'zip02' => $Shippings->getZip02(),
                ),
                'address' =>
                array(
                    'pref' => $Shippings->getPref()->getId(),
                    'addr01' => $Shippings->getAddr01(),
                    'addr02' => $Shippings->getAddr02(),
                ),
                'tel' =>
                array(
                    'tel01' => $Shippings->getTel01(),
                    'tel02' => $Shippings->getTel02(),
                    'tel03' => $Shippings->getTel03(),
                ),
                'fax' =>
                array(
                    'fax01' => $Shippings->getFax01(),
                    'fax02' => $Shippings->getFax02(),
                    'fax03' => $Shippings->getFax03(),
                ),
                'Delivery' => $Shippings->getDelivery()->getId(),
                'DeliveryTime' => $deliveryTime,
                'shipping_delivery_date' => $shippingDeliveryDate,
                'ShipmentItems' => $shipmentItems,
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
            'OrderDetails' => $orderDetail,
            'discount' => $Order->getDiscount(),
            'delivery_fee_total' => $Order->getDeliveryFeeTotal(),
            'charge' => $Order->getCharge(),
            'Payment' => $Order->getPayment()->getId(),
            'Shippings' => $shippings,
            'note' => $Order->getNote(),
        );
        return $order;
    }
}
