<?php

namespace Eccube\Tests\Service\PurchaseFlow\Processor;

use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Cart;
use Eccube\Entity\CartItem;
use Eccube\Service\PurchaseFlow\Processor\UsePointToCustomerPurchaseProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Tests\EccubeTestCase;

class UsePointToCustomerPurchaseProcessorTest extends EccubeTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->BaseInfo = $this->entityManager->find(BaseInfo::class, 1);
        $this->BaseInfo->setBasicPointRate(10);
        $this->Customer = $this->createCustomer();
        $this->Order = $this->createOrder($this->Customer);
        $this->Product = $this->createProduct('テスト商品', 5);
    }

    public function testProcess()
    {
        $this->expected = $this->Customer->getPoint();
        $OriginalOrder = clone $this->Order;
        $processor = new UsePointToCustomerPurchaseProcessor();
        $processor->process($this->Order, new PurchaseContext($OriginalOrder, $this->Customer));
        $this->actual = $this->Customer->getPoint() - $OriginalOrder->getUsePoint();
        $this->verify();
    }

    public function testProcessFailure()
    {
        $this->Order->setUsePoint($this->Customer->getPoint() + 1);
        $OriginalOrder = clone $this->Order;
        $processor = new UsePointToCustomerPurchaseProcessor();
        $ProcessResult = $processor->process($this->Order, new PurchaseContext($OriginalOrder, $this->Customer));
        self::assertTrue($ProcessResult->isError());
    }
}
