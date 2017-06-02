<?php
namespace Eccube\Service\Calculator;

use Eccube\Entity\Order;
use Eccube\Entity\ShipmentItem;
use Eccube\Service\Calculator\Strategy\CalculateStrategyInterface;

class CalculateContext
{
    /* @var Order $Order */
    protected $Order;

    /* @var ShipmentItemCollection $ShipmentItems */
    protected $ShipmentItems = []; // Collection になってる？

    // $app['eccube.calculate.strategies'] に DI する
    /* @var CalculateStrategyInterface[] $CalculateStrategies */
    protected $CalculateStrategies;

    public function executeCalculator()
    {
        foreach ($this->CalculateStrategies as $Strategy) {
            $Strategy->execute($this->ShipmentItems);
        }

        /** @var ShipmentItem $ShipmentItem */
        foreach($this->ShipmentItems as $ShipmentItem) {
            if (!$this->Order->getShipmentItems()->contains($ShipmentItem)) {
                $ShipmentItem->setOrder($this->Order);
                $this->Order->addShipmentItem($ShipmentItem);
                // ここのタイミングで Persist 可能?
            }
        }
        return $this->calculateOrder($this->Order);
    }

    public function calculateOrder(Order $Order)
    {
        // OrderDetails の計算結果を Order にセットする
        $subTotal = $Order->calculateSubTotal();
        $Order->setSubtotal($subTotal);
        $total = $Order->getTotalPrice();
        if ($total < 0) {
            $total = 0;
        }
        $Order->setTotal($total);
        $Order->setPaymentTotal($total);
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

    public function setOrder(Order $Order)
    {
        $this->Order = $Order;
        // ArrayIterator のラップしたクラスを作って明細種別ごとに管理したい
        $this->ShipmentItems = new ShipmentItemCollection($Order->getShipmentItems()->toArray());
    }
}
