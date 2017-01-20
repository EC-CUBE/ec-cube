<?php
namespace Eccube\Service;

use Eccube\Service\Calculator\Strategy\CalculateStrategy;
use Eccube\Service\Calculator\CalculateContext;

class CalculateService
{
    protected $Customer;
    protected $Order;
    protected $ProductClasses = [];
    protected $Deliveries = [];
    protected $Payment;

    protected $CalculateContext;

    public function __construct($Order, $Customer)
    {
        $this->Order = $Order;
        $this->Customer = $Customer;
    }
    public function addCalculator(CalculateStrategy $strategy)
    {
        $this->CalculateContext->CalculateStrategy[] = $strategy;
    }

    /**
     * 単価集計後の Order を返す.
     *
     * @return \Eccube\Entity\Order
     */
    public function calculate()
    {
        $Order = $this->CalculateContext->executeCalculator($this->Order);
        return $Order;
    }

    public function setContext(CalculateContext $Context)
    {
        $this->CalculateContext = $Context;
    }
}
