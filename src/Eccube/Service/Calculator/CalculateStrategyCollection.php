<?php

namespace Eccube\Service\Calculator;

use Eccube\Application;
use Eccube\Entity\Order;
use Eccube\Entity\PurchaseInterface;
use Eccube\Service\Calculator\CalculateStrategyInterface;
class CalculateStrategyCollection extends \Doctrine\Common\Collections\ArrayCollection
{
    protected $app;
    protected $Order;

    public function setApplication(Application $app) {
        $this->app = $app;
    }

    public function setOrder(Purchaseinterface $Order) {
        $this->Order = $Order;
        // TODO DI の定義で宣言的にセットしたい
        foreach ($this as $Strategy) {
            $Strategy->setOrder($this->Order);
        }
    }

    public function add($Strategy)
    {
        // TODO 本当は取り出すときにインスタンス化したい
        $Strategy->setApplication($this->app);
        return parent::add($Strategy);
    }
}
