<?php
namespace Eccube\Service\Calculator;

use Eccube\Entity\Order;
use Eccube\Entity\PurchaseInterface;
use Eccube\Entity\OrderItem;
use Eccube\Service\Calculator\Strategy\CalculateStrategyInterface;

class CalculateContext
{
    /* @var Order $Order */
    protected $Order;

    /* @var OrderItemCollection $OrderItems */
    protected $OrderItems = []; // Collection になってる？

    // $app['eccube.calculate.strategies'] に DI する
    /* @var \Eccube\Service\Calculator\CalculateStrategyCollection CalculateStrategies */
    protected $CalculateStrategies;

    public function executeCalculator()
    {
        $this->buildCalculator($this->CalculateStrategies);

        /** @var OrderItem $OrderItem */
        foreach($this->OrderItems as $OrderItem) {
            if ($OrderItem instanceof OrderItem) {
                if (!$this->Order->getItems()->contains($OrderItem)) {
                    $OrderItem->setOrder($this->Order);
                    $this->Order->addOrderItem($OrderItem);
                    // ここのタイミングで Persist 可能?
                }
            }
        }
        return $this->calculateOrder($this->Order);
    }

    public function buildCalculator(\Eccube\Service\Calculator\CalculateStrategyCollection $strategies)
    {
        foreach ($strategies as $Strategy) {
            if (in_array($this->OrderItems->getType(), $Strategy->getTargetTypes())) {
                $Strategy->execute($this->OrderItems);
            }
        }
    }

    /**
     * TODO
     * 集計は全部ここでやる. 明細を加算するのみ.
     * 計算結果を Order にセットし直すのもここでやる.
     * DI で別クラスにした方がいいかも
     * @deprecated PurchaseFlow::calculate() を使用してください 
     */
    public function calculateOrder(PurchaseInterface $Order)
    {
        if ($this->Order instanceof Order) { // TODO context のほうで判定したい
            $subTotal = $Order->calculateSubTotal();
            $Order->setSubtotal($subTotal);
            $total = $Order->getTotalPrice();
            if ($total < 0) {
                $total = 0;
            }
            $Order->setTotal($total);
            $Order->setPaymentTotal($total);
        }
        return $Order;
    }

    public function setCalculateStrategies(\Eccube\Service\Calculator\CalculateStrategyCollection $strategies)
    {
        $this->CalculateStrategies = $strategies;
    }

    public function getCalculateStrategies()
    {
        return $this->CalculateStrategies;
    }

    public function setOrder(PurchaseInterface $Order)
    {
        $this->Order = $Order;
        $this->OrderItems = new OrderItemCollection($Order->getItems()->toArray(), get_class($this->Order));
    }
}
