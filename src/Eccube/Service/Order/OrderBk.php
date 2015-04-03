<?php

class Order {
    
}

/**
class OrderService {

    function calc() {
        $tax           = 0;
        $point         = 0;
        $deliv_fee     = 0;
        $subtotal      = 0;
        $totalt        = 0;
        $payment_total = 0;
        $carget        = 0;
        $discount      = 0;
        $add_point     = 0;
        $birth_point   = 0;
        
        $tax = $this->calcTax($orderDetails);
        $point = $this->calcPoint($orderDetails);
        $point = $this->calcPoint($orderDetails);
        
        foreach ($orderDetail as $detail) {
            $price = $detail->getPrice();
            $quantity = $detail->getQuantity();
            
            $tax += $this->calcTax($price, $quantity);
        }
        $order['payment_total'] = $total - $use_point - $charge;
    }
}

// sample :管理画面の受注登録を想定

if ($form->isValid()) {
    $form->handleRequest($request);
    /** @var Eccube\Entiry\Order */
    $order = $form->getData();
    /** @var Eccube\Entiry\Customer 注文者情報から、Customerエンティティを生成 */ 
    $customer = $order->getOrderCustomer();
    /** @var Eccube\Entiry\Customer 注文者情報から、Customerエンティティを生成 */ 
    $taxRule = $app['orm.em']->getRepository("Entity\TaxRule")
            ->findCurrentTaxRule();

    $taxCalculater = new TaxCalculater();
    $taxCalculater->setTaxRule($taxRule);
    $taxCalculater->setPrefId($customer->getPrefId());
    $taxCalculater->setCountryId($customer->getCountryId());
    $orderService = $app['eccube.service.order'];
    $orderService->addTaxCalculater($taxCalculater);
    $orderService->
}

interface Calculater {
    /** 
     * 小計(price * quantity + tax)
     * 
     * @param array $orderDetails
     */
    public function calcSubTotal(array $orderDetails);

    public function calcTax(array $orderDetails);
    public function calcPoint(array $orderDetails);
    public function calcDelivFee(array $orderDetails);
    public function calcTotal(array $orderDetails);
}

abstract class AbstractCalculater {
    
}

class TaxCalculater {
    
}



// 受注を生成
$order = new Order();

try {
    $order->begin();
    // 店舗設定（ポイントルール）
    $order->setPointRule($pointRule);
    // 店舗設定（税率ルール）
    $order->setTaxRule($taxRule);

    // 注文者
    $order->setUser($user);
    // 配送業者
    $order->setDeliv($deliv);
    // 支払手段
    $order->setPayment($payment);
    // ☆商品追加
    $order->addProduct($productA, 10 /* quantity */);
    $order->addProduct($productB, 5 /* quantity */);

    // お届け先追加
    $order->addShipping($productA, 8, $user->getShipA());
    $order->addShipping($productA, 2, $user->getShipB());
    $order->addShipping($productB, 5, $user->getShipC());

    // 計算
    $order->calculate();
    
    // 在庫減算
    $order->zaiko();
    // ポイント減算
    $order->point();

    // 確定
    $order->commit();

} catch {
    // エラー時ロールバック
    $order->rollback();

} fianly {
    // do something
}
*/

